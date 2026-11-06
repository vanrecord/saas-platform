<?php

namespace App\Models;

use Spatie\LaravelSettings\Settings;
// use Spatie\MediaLibrary\HasMedia;

class FormatBackgroundQrSettings extends Settings
{
    public array $format_background_qr_school;

    public static function group(): string
    {
        return 'format_qr';
    }
}