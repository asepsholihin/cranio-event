<?php

namespace App\Support;
use App\Models\Participant;
use Illuminate\Support\Str;

class NumberFormat
{
    public static function terbilang($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim(self::penyebut($nilai));
        } else {
            $hasil = trim(self::penyebut($nilai));
        }
        return ucwords($hasil);
    }

    public static function separatorAmount($amount)
    {
        if ($amount < 0) {
            $amount = number_format(abs($amount));
            return "({$amount})";
        }

        return number_format($amount);
    }

    public static function discount($amount)
    {
        $amount = number_format(abs($amount));
        return "({$amount})";
    }

    public static function currencyToString($curr)
    {
        if ($curr == 'Rp')
            return 'Rupiah';
        
        return 'Dollar';
    }

    public static function amountInvoiceDescription($amount)
    {
        if ($amount > 0) {
            return 'Sisa Pembayaran';
        }

        if ($amount < 0) {
            return 'Lebih Bayar';
        }

        return 'LUNAS';
    }
    
    private static function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = self::penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = self::penyebut($nilai / 10) . " puluh" . self::penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . self::penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = self::penyebut($nilai / 100) . " ratus" . self::penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . self::penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = self::penyebut($nilai / 1000) . " ribu" . self::penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = self::penyebut($nilai / 1000000) . " juta" . self::penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = self::penyebut($nilai / 1000000000) . " milyar" . self::penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = self::penyebut($nilai / 1000000000000) . " trilyun" . self::penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    public static function formatPhoneNumberIndonesia($phoneNumber) {
        $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);
    
        if(strlen($phoneNumber) == 13) {
            $countryCode = substr($phoneNumber, 0, 2);
            $areaCode = substr($phoneNumber, -11, 3);
            $nextThree = substr($phoneNumber, -8, 4);
            $lastFour = substr($phoneNumber, -4, 4);
    
            $phoneNumber = '+'.$countryCode.' '.$areaCode.' '.$nextThree.' '.$lastFour;
        }
        if(strlen($phoneNumber) == 12) {
            $countryCode = substr($phoneNumber, 0, 2);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 4);
            $lastFour = substr($phoneNumber, -3, 3);
    
            $phoneNumber = '+'.$countryCode.' '.$areaCode.' '.$nextThree.' '.$lastFour;
        }
        else if(strlen($phoneNumber) == 11) {
            $countryCode = substr($phoneNumber, 0, 2);
            $areaCode = substr($phoneNumber, -9, 3);
            $nextThree = substr($phoneNumber, -6, 3);
            $lastFour = substr($phoneNumber, -3, 3);
    
            $phoneNumber = '+'.$countryCode.' '.$areaCode.' '.$nextThree.' '.$lastFour;
        }
        else if(strlen($phoneNumber) == 10) {
            $countryCode = substr($phoneNumber, 0, 2);
            $areaCode = substr($phoneNumber, -8, 3);
            $nextThree = substr($phoneNumber, -5, 3);
            $lastFour = substr($phoneNumber, -2, 3);
    
            $phoneNumber = '+'.$countryCode.' '.$areaCode.' '.$nextThree.' '.$lastFour;
        }
        else if(strlen($phoneNumber) == 9) {
            $countryCode = substr($phoneNumber, 0, 2);
            $areaCode = substr($phoneNumber, -7, 3);
            $nextThree = substr($phoneNumber, -4, 3);
            $lastFour = substr($phoneNumber, -1, 3);
    
            $phoneNumber = '+'.$countryCode.' '.$areaCode.' '.$nextThree.' '.$lastFour;
        }
        else if(strlen($phoneNumber) == 8) {
            $countryCode = substr($phoneNumber, 0, 2);
            $areaCode = substr($phoneNumber, -6, 3);
            $nextThree = substr($phoneNumber, -3, 3);
    
            $phoneNumber = '+'.$countryCode.' '.$areaCode.' '.$nextThree;
        }
    
        return $phoneNumber;
    }

    public static function formatWhatsappIndonesia($phoneNumber) {
        $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
        }

        if (Str::startsWith($phoneNumber, '8')) {
            $phoneNumber = Participant::PREFIX_PHONE_NUMBER . $phoneNumber;
        }

        return $phoneNumber;
    }

    public static function formatPhoneNumberArab($phoneNumber) {
        $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);
    
        if(strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, 3);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 3);
            $lastFour = substr($phoneNumber, -4, 4);
    
            $phoneNumber = '+'.$countryCode.' '.$areaCode.' '.$nextThree.' '.$lastFour;
        }
        else if(strlen($phoneNumber) == 10) {
            $areaCode = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);
    
            $phoneNumber = $areaCode.' '.$nextThree.' '.$lastFour;
        }
        else if(strlen($phoneNumber) == 7) {
            $nextThree = substr($phoneNumber, 0, 3);
            $lastFour = substr($phoneNumber, 3, 4);
    
            $phoneNumber = $nextThree.'-'.$lastFour;
        }
    
        return $phoneNumber;
    }
}
