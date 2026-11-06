<?php

namespace App\Models;

use Spatie\LaravelSettings\Settings;
// use Spatie\MediaLibrary\HasMedia;

class UploadBackgroundQrSettings extends Settings
{
    public string $upload_background_qr;
    public string $upload_background_qr_school;

    public static function group(): string
    {
        return 'file';
    }
}