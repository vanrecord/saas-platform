<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelSettings\Models\SettingsProperty;
use Spatie\MediaLibrary\HasMedia;
use App\Supports\Trait\MediaLibrary;

class Setting extends SettingsProperty implements HasMedia
{
    use HasFactory;
    use MediaLibrary;

    protected $table = 'settings';

    protected $mediaImage = ['upload_background_qr','upload_background_qr_school'];

    protected $guarded = [];

    protected $casts = [
        'locked' => 'boolean',
    ];
}
