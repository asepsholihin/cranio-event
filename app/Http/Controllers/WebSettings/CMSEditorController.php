<?php

namespace App\Http\Controllers\WebSettings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Requests\ImageSlider\StoreWebSliderRequest;
use App\Http\Requests\ImageSlider\StoreWebWhyusRequest;
use App\Http\Requests\ImageSlider\StoreWebTourPackageRequest;
use App\Http\Requests\ImageSlider\StoreWebProgramRequest;
use App\Http\Requests\ImageSlider\StoreWebPartnerRequest;
use App\Models\WebSlider;
use App\Models\WebWhyus;
use App\Models\WebTourPackage;
use App\Models\WebProgram;
use App\Models\WebPartner;
use App\Models\PriceSimulation;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\Storage;
use Image;


class CMSEditorController extends Controller
{

    public function __construct()
    {
        App::setLocale('id');
        $this->middleware('permission:web-settings-add-or-edit');
    }

    public function editorMainVisual(StoreWebSliderRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
        ]);

        WebSlider::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

        

        return response()->json(['success' => true]);
    }

    public function editorAddMainVisual(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|file|mimes:jpg,png,webp|max:1536'
        ], [
            'file_image.uploaded' => 'Gambar terlalu besar, maximal ukuran 1.5MB'
        ]);

        $overlay_value = 0;
        if ($request->overlay == "true" || $request->overlay == 1) {
            $overlay_value = 1;
        }

        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'title' => $request->title,
            'description' => $request->description,
            'slider_url' => 'https://www.jejakimani.com',
            'status' => 1,
            'overlay' => $overlay_value
        ]);

        if ($request->hasFile(WebSlider::IMAGE)) {
            $imageMake = Image::make($request->file(WebSlider::IMAGE));

            $img =  (string) $imageMake->encode('webp');
            $imgSmall =  (string) $imageMake
                ->resize(500, null, function ($constraint) {$constraint->aspectRatio();})
                ->encode('webp');
                
            $profilePhotoPath =  WebSlider::DIR_IMAGE . pathinfo($request->file(WebSlider::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  WebSlider::DIR_IMAGE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file(WebSlider::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            
            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            $request->merge(['image_url' => $profilePhotoPath]);

            WebSlider::create($request->except(['image']));
        }

        
    }

    public function editorWhyUsSlider(StoreWebWhyusRequest $request)
    {
        $request->validate([
            'title' => 'required|max:40'
        ]);

        $uid = auth()->user()->id;

        $status_value = $request->status;
        if ($request->status == "true")
            $status_value = 1;
        if ($request->status == "false")
            $status_value = 0;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'status_value' => $status_value
        ]);
        
        WebWhyus::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

        
    }

    public function editorAddWhyUsSlider(Request $request)
    {
        $request->validate([
            'title' => 'required|max:40',
            'order' => 'required',
            'image' => 'required|file|mimes:jpg,png,webp|max:1536'
        ], [
            'file_image.uploaded' => 'Gambar terlalu besar, maximal ukuran 1.5MB'
        ]);

        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'title' => $request->title,
            'order' => $request->order,
            'url' => 'https://www.jejakimani.com',
            'status' => 1
        ]);

        if ($request->hasFile(WebWhyus::IMAGE)) {
            $imageMake = Image::make($request->file(WebWhyus::IMAGE));
            $img =  (string) $imageMake->encode('webp');

            $imgSmall =  (string) $imageMake
                ->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp');
                
            $profilePhotoPath =  WebWhyus::DIR_IMAGE . pathinfo($request->file(WebWhyus::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  WebWhyus::DIR_IMAGE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file(WebWhyus::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            $request->merge(['image_url' => $profilePhotoPath]);

            WebWhyus::create($request->except(['image']));
        }

        
    }

    public function editorTourPackageSlider(StoreWebTourPackageRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
        ]);

        WebTourPackage::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

        
    }

    public function editorAddTourPackageSlider(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'order' => 'required',
            'url' => 'required',
            'image' => 'required|file|mimes:jpg,png,webp|max:1536'
        ], [
            'file_image.uploaded' => 'Gambar terlalu besar, maximal ukuran 1.5MB'
        ]);

        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'title' => $request->title,
            'order' => $request->order,
            'url' => $request->url,
        ]);

        if ($request->status == "true")
            $status_value = 1;
        if ($request->status == "false")
            $status_value = 0;

        if ($request->hasFile(WebTourPackage::IMAGE)) {
            $imageMake = Image::make($request->file(WebTourPackage::IMAGE));
            $img =  (string) $imageMake->encode('webp');

            $imgSmall =  (string) $imageMake
            ->resize(500, null, function ($constraint) {$constraint->aspectRatio();})
            ->encode('webp',90);

            $profilePhotoPath =  WebTourPackage::DIR_IMAGE . pathinfo($request->file(WebTourPackage::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  WebTourPackage::DIR_IMAGE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file(WebTourPackage::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            
            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            $request->merge(['image_url' => $profilePhotoPath]);

            WebTourPackage::create($request->except(['image']));
        }

        
    }


    public function editorProgramSlider(StoreWebProgramRequest $request)
    {
        $uid = auth()->user()->id;
        $status_value = $request->status;
        if ($request->status == "true")
            $status_value = 1;
        if ($request->status == "false")
            $status_value = 0;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        WebProgram::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));
    }

    public function editorAddProgramSlider(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'order' => 'required',
            'url' => 'required',
            'image' => 'required|file|mimes:jpg,png,webp|max:1536'
        ], [
            'file_image.uploaded' => 'Gambar terlalu besar, maximal ukuran 1.5MB'
        ]);

        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order,
            'url' => $request->url,
        ]);

        if ($request->hasFile(WebProgram::IMAGE)) {
            $imageMake = Image::make($request->file(WebProgram::IMAGE));
            $img =  (string) $imageMake->encode('webp');

            $imgSmall =  (string) $imageMake
            ->resize(500, null, function ($constraint) {$constraint->aspectRatio();})
            ->encode('webp');
            
            $profilePhotoPath =  WebProgram::DIR_IMAGE . pathinfo($request->file(WebProgram::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  WebProgram::DIR_IMAGE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file(WebProgram::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            $request->merge(['image_url' => $profilePhotoPath]);

            WebProgram::create($request->except(['image']));
        }
    }

    public function editorPartnersSlider(Request $request)
    {
        $uid = auth()->user()->id;

        foreach ($request->id as $key => $id) {
            $updateForm = array();
            $updateForm['order'] = $request->order[$key];
            $updateForm['alt_image'] = $request->alt_image[$key];
            $updateForm['title_image'] = $request->title_image[$key];

            $status_value = $request->status[$key];
            if ($request->status[$key] == "true")
                $status_value = 1;
            if ($request->status[$key] == "false")
                $status_value = 0;

            $updateForm['status'] = $status_value;
            WebPartner::updateOrCreate(['id' => $request->id[$key]], $updateForm);
        }

        
    }

    public function editorPartnerSliderUploadImage(Request $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
        ]);

        if ($request->hasFile(WebPartner::IMAGE)) {
            $imageMake = Image::make($request->file(WebPartner::IMAGE));

            $img =  (string) Image::make($request->file(WebPartner::IMAGE))->encode('webp');
            $imgSmall =  (string) $imageMake
            ->resize(500, null, function ($constraint) {$constraint->aspectRatio();})
            ->encode('webp');

            $profilePhotoPath =  WebPartner::DIR_IMAGE . pathinfo($request->file(WebPartner::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  WebPartner::DIR_IMAGE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file(WebPartner::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            $request->merge(['image_url' => $profilePhotoPath]);

            WebPartner::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));
        }

        
    }

    public function editorAddPartnerSlider(Request $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'url' => 'https://www.jejakimani.com',
        ]);

        if ($request->hasFile(WebPartner::IMAGE)) {
            $imageMake = Image::make($request->file(WebPartner::IMAGE));

            $img =  (string) Image::make($request->file(WebPartner::IMAGE))->encode('webp');
            $imgSmall =  (string) $imageMake
            ->resize(500, null, function ($constraint) {$constraint->aspectRatio();})
            ->encode('webp');

            $profilePhotoPath =  WebPartner::DIR_IMAGE . pathinfo($request->file(WebPartner::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  WebPartner::DIR_IMAGE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file(WebPartner::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            $request->merge(['image_url' => $profilePhotoPath]);

            WebPartner::create($request->except(['image']));
        }

        
    }

    public function imageSliderDelete(Request $request)
    {
        if ($request->section == 'main_visual') {
            $slider = WebSlider::find($request->id);
            if ($slider) {
                $slider->delete();
            }
        }
        if ($request->section == 'why_us_slider') {
            $slider = WebWhyus::find($request->id);
            if ($slider) {
                $slider->delete();
            }
        }
        if ($request->section == 'tour_package_slider') {
            $slider = WebTourPackage::find($request->id);
            if ($slider) {
                $slider->delete();
            }
        }
        if ($request->section == 'program_slider') {
            $slider = WebProgram::find($request->id);
            if ($slider) {
                $slider->delete();
            }
        }
        if ($request->section == 'partner_slider') {
            $slider = WebPartner::find($request->id);
            if ($slider) {
                $slider->delete();
            }
        }

        

        return response()->json(['success' => true]);
    }

    public function priceSimulation(Request $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        PriceSimulation::updateOrCreate(['id' => $request->get('id')], $request->all());

        
    }
}
