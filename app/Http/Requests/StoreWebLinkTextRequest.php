<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\WebLinkText;

class StoreWebLinkTextRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page_id' => 'required|numeric',
            'whatsapp_text' => 'required',
            'status' => 'required',
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $status = $this->status;

        $status_value = 0;
        if ($status == "true") {
            $status_value = 1;
        }
        $this->merge(['status' => $status_value]);
        $this->merge(['lang_id' => 1]);

        $page_name = "Homepage";
        if($this->page_id == 2)
            $page_name = "Contact Us Page";
        if($this->page_id == 3)
            $page_name = "Umrah Parent Page";
        if($this->page_id == 4)
            $page_name = "Product Detail Page";
        if($this->page_id == 5)
            $page_name = "Haji Khusus";
        if($this->page_id == 6)
            $page_name = "Haji Furoda";
        if($this->page_id == 7)
            $page_name = "Wisata Halal";
        if($this->page_id == 8)
            $page_name = "Tombol Daftar";
        if($this->page_id == 9)
            $page_name = "Umrah Bersama Ust. Salim";
        if($this->page_id == 10)
            $page_name = "Umrah Lebih Hemat";
        if($this->page_id == 11)
            $page_name = "Umrah Lebih Nyaman";
        if($this->page_id == 12)
            $page_name = "Tabungan Umrah";
        if($this->page_id == 13)
            $page_name = "Haji Parent Page";
        if($this->page_id == 14)
            $page_name = "Badal Parent";
        if($this->page_id == 15)
            $page_name = "Badal Haji";
        if($this->page_id == 16)
            $page_name = "Badal Umroh";
        // New page
        if($this->page_id == 17)
            $page_name = "Umrah Bersama Ust. Salim Yaqin";
        if($this->page_id == 18)
            $page_name = "Umrah Bersama Ust. Salim Onyx";
        if($this->page_id == 19)
            $page_name = "Umrah Bersama Ust. Salim Ruby";
        if($this->page_id == 20)
            $page_name = "Umrah Bersama Ust. Salim Sapphire";
        if($this->page_id == 21)
            $page_name = "Umrah Bersama Ust. Salim Sapphire Plus";
        if($this->page_id == 22)
            $page_name = "Umrah Lebih Hemat Yaqin";
        if($this->page_id == 23)
            $page_name = "Umrah Lebih Hemat Konsorsium";
        if($this->page_id == 24)
            $page_name = "Umrah Lebih Hemat Onyx";
        if($this->page_id == 25)
            $page_name = "Umrah Lebih Hemat Ruby";
        if($this->page_id == 26)
            $page_name = "Umrah Lebih Nyaman Umrah Plus";
        if($this->page_id == 27)
            $page_name = "Umrah Lebih Nyaman Sapphire";
        if($this->page_id == 28)
            $page_name = "Umrah Lebih Nyaman Sapphire Plus";
        if($this->page_id == 29)
            $page_name = "Google Demand Gen";
        if($this->page_id == 30)
            $page_name = "Google GDN Brand";
        if($this->page_id == 31)
            $page_name = "Google GDN Umroh";
        if($this->page_id == 32)
            $page_name = "Google GDN Haji";
        if($this->page_id == 33)
            $page_name = "Google SEM Haji";
        if($this->page_id == 34)
            $page_name = "Google SEM Umroh";
        if($this->page_id == 35)
            $page_name = "Google Youtube Bumper";
        if($this->page_id == 36)
            $page_name = "Google Youtube CPV";
        if($this->page_id == 37)
            $page_name = "Google Youtube CPM";
        if($this->page_id == 38)
            $page_name = "Meta Awareness Umroh";
        if($this->page_id == 39)
            $page_name = "Meta Awareness Haji";
        if($this->page_id == 40)
            $page_name = "Meta Traffic Umroh";
        if($this->page_id == 41)
            $page_name = "Meta Traffic Haji";
        if($this->page_id == 42)
            $page_name = "Meta Engage Umroh";
        if($this->page_id == 43)
            $page_name = "Meta Engage Haji";
        if($this->page_id == 44)
            $page_name = "Promo Ramadhan";
        if($this->page_id == 45)
            $page_name = "Pop Up Deals";
        $this->merge(['page_name' => $page_name]);
    }
}
