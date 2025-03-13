<?php

namespace App\Http\Requests\WebSettings;

use Illuminate\Foundation\Http\FormRequest;

class StoreIndexPageRequest extends FormRequest
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
        $whyUsTitleRules = 'required|min:3|max:255';
        $aboutTitleRules = 'required|min:3|max:255';
        $profileUstadzTitleRules = 'required|min:3|max:255';
        $tourPackageTitleRules = 'required|min:3|max:255';
        $articleTitleRules = 'required|min:3|max:255';
        $partnerTitleRules = 'required|min:3|max:255';
        $videoUrlRules = 'required|min:3|max:255';
        if (! empty($this->update_only)) {
            $whyUsTitleRules = 'min:3|max:255';
            $aboutTitleRules = 'min:3|max:255';
            $profileUstadzTitleRules = 'min:3|max:255';
            $tourPackageTitleRules = 'min:3|max:255';
            $articleTitleRules = 'min:3|max:255';
            $partnerTitleRules = 'min:3|max:255';
            $videoUrlRules = 'nullable';
        }

        return [
            'why_us_title' => $whyUsTitleRules,
            'about_title' => $aboutTitleRules,
            'about_image' => 'nullable|image|mimes:jpeg,png|max:2048',
            'profile_ustadz_title' => $aboutTitleRules,
            'profile_ustadz_image' => 'nullable|image|mimes:jpeg,png|max:2048',
            'tour_package_title' => $tourPackageTitleRules,
            'article_title' => $articleTitleRules,
            'partner_title' => $partnerTitleRules,
            'video_url' => $videoUrlRules,
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
        if ($status) {
            $status_value = 1;
        }

        $this->merge(['status' => $status_value]);
    }
}
