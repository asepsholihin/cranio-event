<?php
namespace App\File\PDF;

use App\Models\BadalUmrohTrip;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class CertificateBadal
{
    private $pdf;
    private $data;

    public function __construct($badalUmrohTripIds)
    {
        App::setLocale('id');

        $query = BadalUmrohTrip::whereIn('id', $badalUmrohTripIds);
        $badals = $query->select([
            'badal_umroh_trips.*'])
        ->orderByRaw('badal_date DESC')
        ->get();
        
        $data = [
            'badals' => $badals
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.certificate_badal', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('page-size', 'A4')
        ->setOption('margin-top', '0cm')
        ->setOption('margin-left', '0cm')
        ->setOption('margin-right', '0cm')
        ->setOption('margin-bottom', '0cm');
    }

    public function download()
    {   
        return $this->pdf->download("Badal_Certificate_".date('dmy').'.pdf');
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
        return view('pdf.certificate_badal', $this->data)->render();
    }
}
