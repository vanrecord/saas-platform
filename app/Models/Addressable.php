<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addressable extends Model
{
    
    protected $table = 'addressable';

    protected $fillable = [
        'addressable_id',
        'addressable_type',
        'province_id',
        'district_id',
        'village_id',
        'commune_id',
        'house_number',
        'street',
    ];
    public function addressable()
    {
        return $this->morphTo();
    }


}
