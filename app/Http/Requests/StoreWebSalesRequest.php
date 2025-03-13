<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\WebSale;
use App\Models\Participant;
use Image;

class StoreWebSalesRequest extends FormRequest
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
        $userIdRules = 'required|unique:web_sales,user_id,NULL,id,deleted_at,NULL';
        if (! empty($this->id)) {
            $userIdRules = [ 'nullable', Rule::unique('web_sales')->ignore($this->id) ];
        }

        return [
            'user_id' => $userIdRules,
            // 'sales_name' => 'required',
            'order_number' => 'required|numeric',
            'whatsapp_number' => 'required|numeric',
            'whatsapp_api' => 'required',
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
        $phoneNumber = $this->whatsapp_number;

        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
        }

        $status = $this->status;
        $showInFooter = $this->show_in_footer;
        $whatsappService = $this->whatsapp_service;

        $status_value = 0;
        if ($status == "true") {
            $status_value = 1;
        }
        $showInFooter_value = 0;
        if ($showInFooter == "true") {
            $showInFooter_value = 1;
        }
        $whatsappService_value = 0;
        if ($whatsappService == "true") {
            $whatsappService_value = 1;
        }

        $user_id = $this->user_id;

        $name = User::find($user_id)->name;

        $this->merge(['sales_name' => $name ,'status' => $status_value, 'whatsapp_number'=> $phoneNumber, 'show_in_footer'=> $showInFooter_value, 'whatsapp_service'=> $whatsappService_value]);
    }
}
