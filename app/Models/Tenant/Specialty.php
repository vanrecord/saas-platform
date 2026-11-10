<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialty extends Model
{
    use HasFactory;

    protected $table = 'specialties';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_specialties', 'specialty_id', 'doctor_id')
            ->withTimestamps();
    }
}
