<?php
namespace App\File\Image;

use App\Models\Hotel;
use Illuminate\Support\Str;
use SnappyImage;
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
    }
    
    public function download()
    {
        $hotel = $this->hotel;
        
        $img = SnappyImage::
        setOption('width', 500)
        ->setOption('enable-local-file-access', true)
        ->loadView('pdf.hotel_information', compact(['hotel']));
        return $img->download("Hotel ".$hotel->city." - ".$hotel->name.".jpg");
    }

    public function html()
    {
        $hotel = $this->hotel;
        return view('pdf.hotel_information', compact(['hotel']))->render();
    }
}
