<?php

namespace App\Models\Tenant;

use App\Models\Commune;
use App\Models\District;
use App\Models\Province;
use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    protected $fillable = [
        'house_number',
        'street',
        'city',
        'zip_code',
        'province_id',
        'country_id',
        'district_id',
        'commune_id',
        'village_id',
        'addressable_id',
        'addressable_type',
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class, 'commune_id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }
}
