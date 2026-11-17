<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescriptions';

    protected $fillable = [
        'patient_id',
        'prescription',
        'user_id',
        'medical_history_id',
    ];

    protected $casts = [
        'patient_id' => 'integer',
        'user_id' => 'integer',
        'medical_history_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'patient_name',
        'summary',
        'formatted_date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function medicalHistory(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class, 'medical_history_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'user_id');
    }

    public function prescriptionDetails(): HasMany
    {
        return $this->hasMany(PrescriptionDetail::class, 'prescription_id');
    }

    public function getPatientNameAttribute(): string
    {
        if ($this->relationLoaded('patient') && $this->patient) {
            return $this->patient->full_name;
        }
        return '';
    }

    public function getSummaryAttribute(): string
    {
        if (!empty($this->prescription)) {
            return $this->prescription;
        }

        if ($this->relationLoaded('prescriptionDetails') && $this->prescriptionDetails->isNotEmpty()) {
            return $this->prescriptionDetails->map(function ($detail) {
                $medName = $detail->medicine?->name ?: ('Medicine #' . $detail->medicine_id);
                $total = $detail->total_calculated;
                return "{$medName} ({$total} គ្រាប់)";
            })->implode(', ');
        }

        return '-';
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : '';
    }
}
