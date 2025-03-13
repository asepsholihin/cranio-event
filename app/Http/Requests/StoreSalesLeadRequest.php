<?php

namespace App\Http\Requests;

use App\Models\SalesLead;
use App\Models\WebSale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;


class StoreSalesLeadRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'sales_id' => 'required',
            'name' => 'required',
            'country_code' => 'required',
            'no_hp' => 'required|numeric',
            'lead_source_id' => 'required',
            'lead_response' => 'required',
            'package_type_id' => 'required',
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
        $phoneNumber = $this->no_hp;
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', $this->country_code, $phoneNumber);
        }
        $this->merge(['no_hp' => $phoneNumber]);
        
        $salesName = "";
        $salesName = WebSale::find($this->sales_id)->sales_name ?? '';
        $this->merge(['sales_name' => $salesName]);
        
    }
}
