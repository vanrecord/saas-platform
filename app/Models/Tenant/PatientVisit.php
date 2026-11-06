<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patient_visits';

    protected $fillable = [
        'patient_id',
        'visit_number',
        'workflow_option',
        'invoice_reference',
        'visit_type',
        'doctor_id',
        'doctor_name',
        'chief_complaint',
        'clinical_notes',
        'reception_type',
        'outstanding_balance',
        'status',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'outstanding_balance' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public static function generateVisitNumber(): string
    {
        do {
            $candidate = 'VIS-' . date('Ymd') . '-' . str_pad((string) random_int(100, 999), 3, '0', STR_PAD_LEFT);
        } while (static::withTrashed()->where('visit_number', $candidate)->exists());

        return $candidate;
    }

    public static function generateInvoiceReference(): string
    {
        return 'INV-' . date('Ymd') . '-' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    }
}

