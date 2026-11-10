<?php

namespace App\Models\Tenant;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

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
        $maxIdNumber = static::max('id_number');
        $maxId = static::max('id');
        $max = max($maxIdNumber ?? 0, $maxId ?? 0);
        return max($max + 1, 571);
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
     * Resolve avatar public URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $avatar = $this->attributes['avatar'] ?? null;
        if (empty($avatar)) {
            return null;
        }

        if (filter_var($avatar, FILTER_VALIDATE_URL)) {
            return $avatar;
        }

        return Storage::disk('public')->url($avatar);
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

    public function dentalCharts(): HasMany
    {
        return $this->hasMany(DentalChart::class, 'patient_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(PatientVisit::class, 'patient_id');
    }
}
