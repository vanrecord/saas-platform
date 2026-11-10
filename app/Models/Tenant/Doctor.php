<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doctors';

    protected $fillable = [
        'hospital_id',
        'user_id',
    ];

    protected $appends = [
        'full_name',
        'email',
        'gender',
        'avatar_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'user_id');
    }

    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialties', 'doctor_id', 'specialty_id')
            ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        if ($this->relationLoaded('user') && $this->user) {
            if ($this->user->relationLoaded('profile') && $this->user->profile) {
                return $this->user->profile->full_name ?: ($this->user->name ?? '');
            }
            return $this->user->name ?? '';
        }

        return '';
    }

    public function getEmailAttribute(): ?string
    {
        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->email;
        }

        return null;
    }

    public function getGenderAttribute(): string
    {
        if ($this->relationLoaded('user') && $this->user && $this->user->relationLoaded('profile') && $this->user->profile) {
            return $this->user->profile->gender ?? 'Female';
        }

        return 'Female';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->relationLoaded('user') && $this->user && $this->user->relationLoaded('profile') && $this->user->profile) {
            return $this->user->profile->avatar_url;
        }

        return null;
    }
}
