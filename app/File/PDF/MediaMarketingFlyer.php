<?php
namespace App\File\PDF;

use App\Models\MediaMarketing;
use App\Models\MediaMarketingImage;
use App\Models\WebSale;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class MediaMarketingFlyer
{
    private $mediaMarketing;
    private $sales;
    private $pdf;
    private $data;
    private $images;

    public function __construct($mediaMarketing)
    {
        App::setLocale('id');

        $query = MediaMarketingImage::where('media_marketing_id', $mediaMarketing->id);
        $images = $query->select([
            'order',
            'image_url'
        ])
        ->orderBy('order','ASC')
        ->get();

        $sales = WebSale::select('sales_name as name', 'whatsapp_number')->where('user_id', auth()->user()->id)->first() ?? \App\Models\User::find(auth()->user()->id);

        $this->images = $images;
        $data = [
            'sales' => $sales,
            'images' => $images,
            'mediaMarketing' => $mediaMarketing,
            'custom_name' => request()->custom_name,
            'custom_no_hp' => request()->custom_no_hp
        ];
        $this->data = $data;
        $this->mediaMarketing = $mediaMarketing;
        $this->sales = $sales;

        $this->pdf = PDF::loadView('pdf.media_marketing_flyer', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('page-width', "207mm")
        ->setOption('page-height', '260mm')
        ->setOption('margin-top', '0cm')
        ->setOption('margin-left', '0cm')
        ->setOption('margin-right', '0cm')
        ->setOption('margin-bottom', '0cm');
    }

    public function download()
    {   
        return $this->pdf->download($this->mediaMarketing->title."_".date('dmy')."_".$this->sales->name.'.pdf');
    }

    public function output()
    {   
        return $this->pdf->output();
    }

    public function stream()
    {   
        return $this->pdf->stream();
    }

    public function html()
    {   
        return view('pdf.media_marketing_flyer', $this->data)->render();
    }
}
