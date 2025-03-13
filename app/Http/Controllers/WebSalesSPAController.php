<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWebSalesRequest;
use App\Models\WebSale;
use App\Models\MasterOffice;
use App\Support\NumberFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\WebLinkText;
use Illuminate\Support\Facades\DB;
use Meema\CloudFront\Facades\CloudFront;
use Meema\CloudFront\Jobs\InvalidateCache;

class WebSalesSPAController extends Controller
{
    const SPA_PATH = '/web-sales';

    public function __construct()
    {
        $this->middleware('permission:booking-order-view|web-sales-view')->only(['queryWebSales']);
        $this->middleware('permission:web-sales-view')->only(['index', 'show']);
        $this->middleware('permission:web-sales-add-or-edit')->only(['store', 'import']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = request()->query('sortBy', 'id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        return response()->json(
            WebSale::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreWebSalesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebSalesRequest $request)
    {
        WebSale::updateOrCreate(['id' => $request->get('id')], $request->except(['icon']));
        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WebSale  $webSale
     * @return \Illuminate\Http\Response
     */
    public function show(WebSale $webSale)
    {
        return response()->json($webSale->toArray());
    }

    public function destroy(WebSale $webSale)
    {
        $webSale->delete();
    }

    public function queryWebSales(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $query = WebSale::select(['web_sales.id', 'web_sales.user_id', 'sales_name', 'whatsapp_number', 'whatsapp_api', 'order_number'])
        ->where(function($q) use($search) {
            $q->where('sales_name', 'like', $search)
            ->orWhere('whatsapp_number', 'like', $search)
            ->orWhere('order_number', 'like', $search);
        });
        if(auth()->user()->id != 1) {
            // Bukan Admin
            $query->join('users', 'users.id', 'web_sales.user_id')->join('master_office_user', 'master_office_user.user_id', 'users.id');
            $query->whereIn('master_office_user.office_id', auth()->user()->office_ids);
        }
        $result = $query->where('web_sales.status', 1)->groupBy('web_sales.id')->get();
        return response()->json($result);
    }

    public function contactSales(Request $request)
    {
        if ($request->has('pageId')) {
            $linkText = WebLinkText::where('page_id', $request->pageId)->first();
        } else {
            $linkText = WebLinkText::where('page_name', 'like', '%'.$request->pageName.'%')->first();
        }
        $whatsapp_text = $linkText->whatsapp_text;
        if ($request->productName) {
            $whatsapp_text = str_replace("@product_name", $request->productName, $linkText->whatsapp_text);
        }
        if ($request->categoryName) {
            $whatsapp_text = str_replace("@category_name", $request->categoryName, $linkText->whatsapp_text);
        }
        if ($request->subcategoryName) {
            $whatsapp_text = str_replace("@subcategory_name", $request->subcategoryName, $linkText->whatsapp_text);
        }

        // Log sales kosong untuk pertama kali
        $lastLog = DB::table('web_log_sales')->orderBy('id', 'desc')->first();
        if (empty($lastLog)) {
            $salesman = WebSale::orderBy('order_number', 'ASC')->where('status', 1)->where('whatsapp_service', 1)->first();
            DB::table('web_log_sales')->insert([
                'web_sales_id' => $salesman->id,
                'order_number' => $salesman->order_number,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            WebSale::where('id', $salesman->id)->update([
                'total_visit' => ($salesman->total_visit + 1)
            ]);
            $whatsapp_text = str_replace("@sales_name", $salesman->sales_name, $whatsapp_text);
            $template = "https://api.whatsapp.com/send/?phone=" . $salesman->whatsapp_number . "&text=" . $whatsapp_text . "";
            return response()->json([
                'whatsapp_number' => $salesman->whatsapp_number,
                'whatsapp_api' => $template,
                'google_tag_event' => $linkText->google_tag_event,
                'google_tag_event_category' => $linkText->google_tag_event_category,
                'google_tag_event_label' => $linkText->google_tag_event_label,
            ]);
        }

        // Sales yang belum kebagian
        $salesman = WebSale::where('order_number', ($lastLog->order_number + 1))->where('status', 1)->where('whatsapp_service', 1)->first();
        if ($salesman) {
            DB::table('web_log_sales')->insert([
                'web_sales_id' => $salesman->id,
                'order_number' => $salesman->order_number,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            WebSale::where('id', $salesman->id)->update([
                'total_visit' => ($salesman->total_visit + 1)
            ]);
            $whatsapp_text = str_replace("@sales_name", $salesman->sales_name, $whatsapp_text);
            $template = "https://api.whatsapp.com/send/?phone=" . $salesman->whatsapp_number . "&text=" . $whatsapp_text . "";
            return response()->json([
                'whatsapp_number' => $salesman->whatsapp_number,
                'whatsapp_api' => $template,
                'google_tag_event' => $linkText->google_tag_event,
                'google_tag_event_category' => $linkText->google_tag_event_category,
                'google_tag_event_label' => $linkText->google_tag_event_label,
            ]);
        }

        // Sales sudah habis giliran balik lagi
        $salesman = WebSale::orderBy('order_number', 'ASC')->where('status', 1)->where('whatsapp_service', 1)->first();
        DB::table('web_log_sales')->insert([
            'web_sales_id' => $salesman->id,
            'order_number' => $salesman->order_number,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        WebSale::where('id', $salesman->id)->update([
            'total_visit' => ($salesman->total_visit + 1)
        ]);
        $whatsapp_text = str_replace("@sales_name", $salesman->sales_name, $whatsapp_text);
        $template = "https://api.whatsapp.com/send/?phone=" . $salesman->whatsapp_number . "&text=" . $whatsapp_text . "";
        return response()->json([
            'whatsapp_number' => $salesman->whatsapp_number,
            'whatsapp_api' => $template,
            'google_tag_event' => $linkText->google_tag_event,
            'google_tag_event_category' => $linkText->google_tag_event_category,
            'google_tag_event_label' => $linkText->google_tag_event_label,
        ]);
    }

    public function contactOffice(Request $request)
    {
        $office = MasterOffice::find($request->officeId);
        $whatsapp_text = $office->whatsapp_link;
        $whatsapp_number = NumberFormat::formatWhatsappIndonesia($office->office_phone);
        $template = "https://api.whatsapp.com/send/?phone=" . $whatsapp_number . "&text=" . $whatsapp_text . "";
        return response()->json([
            'whatsapp_number' => $whatsapp_number,
            'whatsapp_api' => $template,
            'google_tag_event' => '',
            'google_tag_event_category' => '',
            'google_tag_event_label' => '',
        ]);
    }

    public function salesInFooter(Request $request)
    {
        $sales = WebSale::select(['id', 'user_id', 'sales_name', 'whatsapp_number', 'whatsapp_api', 'order_number'])
            ->where('status', 1)
            ->where('show_in_footer', 1)
            ->orderBy('order_number', 'asc')
            ->get();
        return response()->json($sales);
    }
}
