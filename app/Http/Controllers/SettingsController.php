<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\FormatBackgroundQrSettings;
use App\Models\UploadBackgroundQrSettings;
use Spatie\MediaLibrary\InteractsWithMedia;

class SettingsController extends Controller
{
    use InteractsWithMedia;
    public function __construct()
    {
       $this->uploadBackgroundQrSettings = app(UploadBackgroundQrSettings::class);
       $this->formatBackgroundQrSettings = app(FormatBackgroundQrSettings::class);
       $this->setting = app(Setting::class);
    }

    public function index()
    {
        $this->authorize(app("request")->route()->getName(), Setting::class);
        $file_upload = null;
        // $bg_qr_school= null;
        $setting = $this->setting->where('name','upload_background_qr')->first();
        // $setting_bg_qr_school = $this->setting->where('name','upload_background_qr_school')->first();
        if($this->uploadBackgroundQrSettings->upload_background_qr){
            $file_upload = $this->getFileInTemporary($setting->upload_background_qr);
        }
        // if($this->uploadBackgroundQrSettings->upload_background_qr_school){
        //     $bg_qr_school = $this->getFileInTemporary($setting_bg_qr_school->upload_background_qr_school);
        // }
        // $format_background_qr_school = count($this->formatBackgroundQrSettings->format_background_qr_school)> 0 ? $this->formatBackgroundQrSettings->format_background_qr_school[0]: [];
        
        return Inertia::render('Setting/Index', [
            'upload_background_qr' => $file_upload,
            // 'upload_background_qr_school' => $bg_qr_school,
            // 'format_background_qr_school' => $format_background_qr_school,
        ]);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'upload_background_qr' =>'max:2048',
            // 'upload_background_qr_school' => 'max:2048',
        ])->validate();
        $imageName = '';
        // $imageNameSchool = '';
        $setting = $this->setting->where('name','upload_background_qr')->first();
        $imageName = $this->saveMediaQR($setting, 'upload_background_qr', $request);
        // $setting_bg_school = $this->setting->where('name','upload_background_qr_school')->first();
        // $imageNameSchool = $this->saveMediaQR($setting_bg_school, 'upload_background_qr_school', $request);
        $this->uploadBackgroundQrSettings->upload_background_qr = $imageName;
        // $this->uploadBackgroundQrSettings->upload_background_qr_school = $imageNameSchool;
        $this->uploadBackgroundQrSettings->save();
        
        // $this->saveFormatBackgroundQrSettings($request->format_background_qr_school);

        return redirect()->back();
    }
    public function getFileInTemporary($files)
    {
        $FileUpload =[];
        foreach($files as $file){
            $temp_url = $file->getTemporaryUrl(\Carbon\Carbon::now()->addMinutes(10));
            $FileUpload[] = [
                'source' => [
                    'id' => $file->id,
                    'url' => $temp_url
                ],
                'options' => [
                    'type' => 'local',
                    'file' => [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'size' => $file->size,
                        'type' => $file->mime_type,
                    ],
                    'metadata' => [
                        'poster' => $temp_url,
                    ],
                ],
            ];
        }
        return $FileUpload;
    }

    public function saveMediaQR($model, $key, $request)
    {   
        $imageName = '';
        if($request->hasFile($key)){
            $model->getMedia($key)->each->delete();
            $file = $request->file($key);
            $imageName = $file->getClientOriginalName();
            $model
                ->addMediaFromRequest($key)
                ->toMediaCollection($key);
        }else if(empty($request[$key])){
            $model->getMedia($key)->each->delete();
        }else if (count($request[$key]) > 0){
            $imageName = $request[$key]['name'];
        }
        return $imageName;
    }

    // public function saveFormatBackgroundQrSettings($format_background_qr_school)
    // {
    //     $this->formatBackgroundQrSettings->format_background_qr_school = array($format_background_qr_school);
    //     $this->formatBackgroundQrSettings->save();
    // }



}
