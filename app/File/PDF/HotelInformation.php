<?php
namespace App\File\PDF;

use App\Models\Hotel;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class HotelInformation
{
    private $hotel;
    private $pdf;
    private $data;

    public function __construct($hotelId)
    {
        App::setLocale('id');

        $hotel = Hotel::select(['master_hotels.*','master_cities.name as city'])
        ->join('master_cities', 'master_cities.id', 'master_hotels.city_id')
        ->findOrFail($hotelId);
        $this->hotel = $hotel;
        
        $data = [
            'hotel' => $hotel
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.hotel_information', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('page-size', 'A5')
        ->setOption('margin-top', '2cm')
        ->setOption('margin-left', '2cm')
        ->setOption('margin-right', '2cm')
        ->setOption('margin-bottom', '2cm');
    }

    public function download()
    {   
        return $this->pdf->download("Hotel ".$this->hotel->city." - ".$this->hotel->name.'.pdf');
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
        return view('pdf.hotel_information', $this->data)->render();
    }
}
