<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWebLinkTextRequest;
use App\Models\WebLinkText;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class WebLinkTextSPAController extends Controller
{
    const SPA_PATH = '/web-link-text';

    public function __construct()
    {
        $this->middleware('permission:web-link-text-view')->only(['index','show','queryWebLinkText','barcode']);
        $this->middleware('permission:web-link-text-add-or-edit')->only(['store','import']);
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
            WebLinkText::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreWebLinkTextRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebLinkTextRequest $request)
    {
        WebLinkText::updateOrCreate(['id' => $request->get('id')], $request->except(['icon']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WebLinkText  $webLinkText
     * @return \Illuminate\Http\Response
     */
    public function show(WebLinkText $webLinkText)
    {
        return response()->json($webLinkText->toArray());
    }

    public function destroy(WebLinkText $webLinkText)
    {
        $webLinkText->delete();
    }

    public function queryWebLinkText(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = WebLinkText::select(['id','sales_name','whatsapp_number','whatsapp_api','order_number'])
            ->where('sales_name',  $request->get('q'))
            ->orWhere('whatsapp_number', 'like', $search)
            ->orWhere('order_number', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }

    public function contactSales()
    {
        // Log sales kosong untuk pertama kali
        $lastLog = DB::table('web_log_sales')->orderBy('id', 'desc')->first();
        if(empty($lastLog)) {
            $salesman = WebLinkText::orderBy('order_number', 'ASC')->where('status', 1)->first();
            DB::table('web_log_sales')->insert([
                'web_sales_id' => $salesman->id,
                'order_number' => $salesman->order_number,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            WebLinkText::where('id', $salesman->id)->update([
                'total_visit' => ($salesman->total_visit + 1)
            ]);
            return $salesman->only(['sales_name', 'whatsapp_number', 'whatsapp_api']);
        }
        
        // Sales yang belum kebagian
        $salesman = WebLinkText::where('order_number', ($lastLog->order_number + 1))->where('status', 1)->first();
        if($salesman) {
            DB::table('web_log_sales')->insert([
                'web_sales_id' => $salesman->id,
                'order_number' => $salesman->order_number,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            WebLinkText::where('id', $salesman->id)->update([
                'total_visit' => ($salesman->total_visit + 1)
            ]);
            return $salesman->only(['sales_name', 'whatsapp_number', 'whatsapp_api']);
        }

        // Sales sudah habis giliran balik lagi
        $salesman = WebLinkText::orderBy('order_number', 'ASC')->where('status', 1)->first();
        DB::table('web_log_sales')->insert([
            'web_sales_id' => $salesman->id,
            'order_number' => $salesman->order_number,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        WebLinkText::where('id', $salesman->id)->update([
            'total_visit' => ($salesman->total_visit + 1)
        ]);
        return $salesman->only(['sales_name', 'whatsapp_number', 'whatsapp_api']);
    }
}
