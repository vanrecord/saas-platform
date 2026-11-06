<?php

namespace App\Models;

use App\Models\Addressable;
use Illuminate\Database\Eloquent\Model;
use App\Supports\Trait\MediaLibrary; 
use Spatie\MediaLibrary\HasMedia;

class Company extends Model implements HasMedia
{
    use MediaLibrary;
    protected $table = 'companies';
    protected $mediaImage = ['logo'];

    protected $fillable = [
        'name_native',
        'name_international',
        'phone',
        'email',
        'social_links',
        'range',
        'latlng',
        'created_by',
        'updated_by',
        'deleted_by',
        'locked_division'
    ];

    public function addressable()
    {
        return $this->morphOne(Addressable::class, 'addressable');
    }
}
