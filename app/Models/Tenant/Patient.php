<?php

namespace App\Models\Tenant;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Patient extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'patients';

    protected $fillable = [
        'firstname',
        'lastname',
        'age',
        'gender',
        'avatar',
        'phone',
        'address',
        'id_number',
        'email',
        'profession',
        'dob',
        'balance',
        'card_activated',
        'card_code',
        'is_trail_card',
    ];

    protected $casts = [
        'dob' => 'date:Y-m-d',
        'age' => 'integer',
        'id_number' => 'integer',
        'balance' => 'double',
        'card_activated' => 'boolean',
        'is_trail_card' => 'boolean',
    ];

    protected $appends = [
        'full_name',
        'patient_number',
        'avatar_url',
    ];

    /**
     * Compute full name from firstname and lastname.
     */
    public function getFullNameAttribute(): string
    {
        $first = $this->attributes['firstname'] ?? '';
        $last = $this->attributes['lastname'] ?? '';
        $name = trim($first . ' ' . $last);
        return $name !== '' ? $name : ('Patient #' . ($this->id ?? ''));
    }

    public function setGenderAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['gender'] = null;
            return;
        }

        $first = strtoupper(substr(trim((string) $value), 0, 1));
        $this->attributes['gender'] = $first === 'M' ? 'M' : ($first === 'F' ? 'F' : $value);
    }

    /**
     * Generate next sequential patient id_number.
     */
    public static function getNextIdNumber(): int
    {
        $latest = static::whereNotNull('id_number')->orderByDesc('id_number')->withTrashed()->first();
        return $latest ? ((int) $latest->id_number + 1) : 1;
    }

    /**
     * Display identifier based on id_number or fallback to formatted primary ID.
     */
    public function getPatientNumberAttribute(): string
    {
        if (isset($this->attributes['id_number']) && $this->attributes['id_number'] !== null) {
            return (string) $this->attributes['id_number'];
        }
        return 'P' . str_pad((string) ($this->id ?? 0), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Register Spatie media collection.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useDisk('public')
            ->singleFile();
    }

    /**
     * Resolve avatar public URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('avatar');
        if ($media) {
            if ($media->disk === 'public' || $media->disk === 'media') {
                return assetStorage($media->getUrl());
            }
            return $media->getTemporaryUrl(now()->addMinutes(120));
        }

        $avatar = $this->attributes['avatar'] ?? null;
        if (empty($avatar)) {
            return null;
        }

        if (filter_var($avatar, FILTER_VALIDATE_URL)) {
            return $avatar;
        }

        return assetStorage(Storage::disk('public')->url($avatar));
    }

    /**
     * Calculate age from dob if not explicitly provided.
     */
    public function getAgeAttribute($value): ?int
    {
        if ($value !== null) {
            return (int) $value;
        }

        if (!empty($this->attributes['dob'])) {
            try {
                return (int) Carbon::parse($this->attributes['dob'])->age;
            } catch (\Throwable $e) {}
        }

        return null;
    }

    // Aliases to maintain compatibility with other modules (visits, odontogram, etc.)
    public function getFirstNameAttribute(): string
    {
        return $this->attributes['firstname'] ?? '';
    }

    public function getLastNameAttribute(): string
    {
        return $this->attributes['lastname'] ?? '';
    }

    public function getDateOfBirthAttribute(): ?string
    {
        return isset($this->attributes['dob']) ? substr($this->attributes['dob'], 0, 10) : null;
    }

    public function getSexAttribute(): ?string
    {
        return $this->attributes['gender'] ?? null;
    }

    public function getOccupationAttribute(): ?string
    {
        return $this->attributes['profession'] ?? null;
    }

    public function medicalHistories(): HasMany
    {
        return $this->hasMany(MedicalHistory::class, 'patient_id');
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class, 'patient_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

    public function receptions(): HasMany
    {
        return $this->hasMany(Reception::class, 'patient_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Reception::class, 'patient_id');
    }

    public function addressRecord()
    {
        return $this->morphOne(\App\Models\Tenant\Address::class, 'addressable');
    }

    public function getFormattedAddressAttribute(): string
    {
        if (!empty($this->attributes['address'])) {
            return $this->attributes['address'];
        }

        $addr = $this->relationLoaded('addressRecord')
            ? $this->addressRecord
            : $this->addressRecord()->with(['village', 'commune', 'district', 'province'])->first();

        if ($addr) {
            $parts = array_filter([
                $addr->house_number ? 'No. ' . $addr->house_number : null,
                $addr->street ? 'St. ' . $addr->street : null,
                $addr->village?->name ?? $addr->village?->name_kh ?? null,
                $addr->commune?->name ?? $addr->commune?->name_kh ?? null,
                $addr->district?->name ?? $addr->district?->name_kh ?? null,
                $addr->province?->name ?? $addr->province?->name_kh ?? $addr->city ?? null,
            ]);
            return implode(', ', $parts);
        }

        return '';
    }
}
