<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'contact_person',
    ];

    protected $appends = [
        'formatted_address',
    ];

    public function addressRecord(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function getFormattedAddressAttribute(): ?string
    {
        if ($this->relationLoaded('addressRecord') && $this->addressRecord) {
            $parts = array_filter([
                $this->addressRecord->village?->name_en,
                $this->addressRecord->commune?->name_en,
                $this->addressRecord->district?->name_en,
                $this->addressRecord->province?->name_en,
            ]);

            if (!empty($parts)) {
                return implode(', ', $parts);
            }
        }

        return $this->address;
    }
}
