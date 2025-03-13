<?php

namespace App\Support;
use Illuminate\Support\Str;

class General
{
    public static function tanggalHijriah($date=null, $withSpace=false)
    {
        $y = date('Y');
        $m = date('m');
        $d = date('d');
        if($date) {
            $y = date('Y', strtotime($date));
            $m = date('m', strtotime($date));
            $d = date('d', strtotime($date));
        }
        $jd = GregoriantoJD($m, $d, $y);
        $l = $jd - 1948440 + 10632;
        $n = (int) (( $l - 1 ) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = ( (int) (( 10985 - $l ) / 5316)) * ( (int) (( 50 * $l) / 17719)) + (
        (int) ( $l / 5670 )) * ( (int) (( 43 * $l ) / 15238 ));
        $l = $l - ( (int) (( 30 - $j ) / 15 )) * ( (int) (( 17719 * $j ) / 50)) - (
        (int) ( $j / 16 )) * ( (int) (( 15238 * $j ) / 43 )) + 29;
        $m = (int) (( 24 * $l ) / 709 );
        $d = $l - (int) (( 709 * $m ) / 24);
        $y = 30 * $n + $j - 30;
        
        $bulanHijriah = array(1 => "Muharram", "Shofar", "Robi'ul Awwal", "Robi'uts Tsani",
        "Jumadil Ula", "Jumadil Akhiroh", "Rojab", "Sya'ban",
        "Romadhon", "Syawwal", "Dzulqo'dah", "Dzulhijjah");

        $hijriah = $d .' '. $bulanHijriah[$m] .' '. $y . 'H';
        if($withSpace)
            $hijriah = $d .' '. $bulanHijriah[$m] .' '. $y . ' H';
        
        return $hijriah;
    }

    public static function tahunHijriah($year=null, $withSpace=false)
    {
        $y = date('Y');
        if($year) {
            $y = $year;
        }
        $m = date('m');
        $d = date('d');
        $jd = GregoriantoJD($m, $d, $y);
        $l = $jd - 1948440 + 10632;
        $n = (int) (( $l - 1 ) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = ( (int) (( 10985 - $l ) / 5316)) * ( (int) (( 50 * $l) / 17719)) + (
        (int) ( $l / 5670 )) * ( (int) (( 43 * $l ) / 15238 ));
        $l = $l - ( (int) (( 30 - $j ) / 15 )) * ( (int) (( 17719 * $j ) / 50)) - (
        (int) ( $j / 16 )) * ( (int) (( 15238 * $j ) / 43 )) + 29;
        $m = (int) (( 24 * $l ) / 709 );
        $d = $l - (int) (( 709 * $m ) / 24);
        $y = 30 * $n + $j - 30;
        
        $bulanHijriah = array(1 => "Muharram", "Shofar", "Robi'ul Awwal", "Robi'uts Tsani",
        "Jumadil Ula", "Jumadil Akhiroh", "Rojab", "Sya'ban",
        "Romadhon", "Syawwal", "Dzulqo'dah", "Dzulhijjah");

        $hijriah = $y . 'H';
        if($withSpace)
            $hijriah = $y . ' H';
        
        return $hijriah;
    }

    public static function tahunHijriahAlt($date=null, $withSpace=false)
    {
        $y = date('Y');
        $m = date('m');
        $d = date('d');
        if($date) {
            $y = date('Y', strtotime($date));
            $m = date('m', strtotime($date));
            $d = date('d', strtotime($date));
        }
        $jd = GregoriantoJD($m, $d, $y);
        $l = $jd - 1948440 + 10632;
        $n = (int) (( $l - 1 ) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = ( (int) (( 10985 - $l ) / 5316)) * ( (int) (( 50 * $l) / 17719)) + (
        (int) ( $l / 5670 )) * ( (int) (( 43 * $l ) / 15238 ));
        $l = $l - ( (int) (( 30 - $j ) / 15 )) * ( (int) (( 17719 * $j ) / 50)) - (
        (int) ( $j / 16 )) * ( (int) (( 15238 * $j ) / 43 )) + 29;
        $m = (int) (( 24 * $l ) / 709 );
        $d = $l - (int) (( 709 * $m ) / 24);
        $y = 30 * $n + $j - 30;
        
        $bulanHijriah = array(1 => "Muharram", "Shofar", "Robi'ul Awwal", "Robi'uts Tsani",
        "Jumadil Ula", "Jumadil Akhiroh", "Rojab", "Sya'ban",
        "Romadhon", "Syawwal", "Dzulqo'dah", "Dzulhijjah");

        $hijriah = $y . 'H';
        if($withSpace)
            $hijriah = $y . ' H';
        
        return $hijriah;
    }
}