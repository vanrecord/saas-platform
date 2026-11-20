<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Target extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'targets';

    protected $fillable = [
        'start_date',
        'end_date',
        'doctor_id',
        'amount',
        'by_doctor',
        'description',
        'user_id',
        'hospital_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'by_doctor' => 'boolean',
        'doctor_id' => 'integer',
        'user_id' => 'integer',
        'hospital_id' => 'integer',
    ];

    protected $appends = [
        'is_verified',
        'start_date_formatted',
        'end_date_formatted',
        'amount_formatted',
        'doctor_name',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'user_id');
    }

    /**
     * Check if target is verified.
     * Uses attribute if present in schema, otherwise defaults to false.
     */
    public function getIsVerifiedAttribute(): bool
    {
        return (bool) ($this->attributes['is_verified'] ?? false);
    }

    public function getStartDateAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function setStartDateAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['start_date'] = null;
        } elseif ($value instanceof Carbon) {
            $this->attributes['start_date'] = $value->startOfDay();
        } else {
            $cleaned = trim((string) $value);
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $cleaned)) {
                $this->attributes['start_date'] = Carbon::createFromFormat('d/m/Y', $cleaned)->startOfDay();
            } else {
                $this->attributes['start_date'] = Carbon::parse($cleaned)->startOfDay();
            }
        }
    }

    public function getEndDateAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function setEndDateAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['end_date'] = null;
        } elseif ($value instanceof Carbon) {
            $this->attributes['end_date'] = $value->endOfDay();
        } else {
            $cleaned = trim((string) $value);
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $cleaned)) {
                $this->attributes['end_date'] = Carbon::createFromFormat('d/m/Y', $cleaned)->endOfDay();
            } else {
                $this->attributes['end_date'] = Carbon::parse($cleaned)->endOfDay();
            }
        }
    }

    public function getStartDateFormattedAttribute(): string
    {
        $raw = $this->getRawOriginal('start_date');
        if (empty($raw)) {
            return '-';
        }
        return Carbon::parse($raw)->format('d/m/Y');
    }

    public function getEndDateFormattedAttribute(): string
    {
        $raw = $this->getRawOriginal('end_date');
        if (empty($raw)) {
            return '-';
        }
        return Carbon::parse($raw)->format('d/m/Y');
    }

    public function getAmountFormattedAttribute(): string
    {
        return '$' . number_format((float) ($this->amount ?? 0), 2);
    }

    public function getDoctorNameAttribute(): string
    {
        if (!$this->by_doctor || !$this->doctor_id) {
            return '-';
        }

        if ($this->relationLoaded('doctor') && $this->doctor) {
            return $this->doctor->full_name ?: ($this->doctor->user?->name ?? "Doctor #{$this->doctor_id}");
        }

        return "Doctor #{$this->doctor_id}";
    }
}
