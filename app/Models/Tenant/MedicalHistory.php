<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalHistory extends Model
{
    use HasFactory, SoftDeletes;
    
    const REQUESTED = 0;
    const SCHEDULED = 1;
    const COMPLETED = 2;
    const CANCELED  = 3;

    const PARTIAL           = 1;
    const UNPAID            = 2;
    const PAID              = 3;
    const REFUNED           = 4;
    const PARTIALLY_REFUNED = 5;

    protected $table = 'medical_histories';

    protected $fillable = [
        'doctor_id',
        'user_id',
        'patient_id',
        'description',
        'diagnosis',
        'medical_alert',
        'rate',
        'discount_invoice',
        'status',
        'date',
        'employee_id',
        'invoice_id',
        'discount_inv_as',
        'term',
        'chief_complaint',
        'estimate_id',
        'type',
        'id_usage',
        'requested_by',
        'vattin',
        'show_vattin',
        'prefix_invoice',
        'invoice_num_year_series',
        'prefix_format',
        'prefix_id',
        'visit_type',
        'invoice_number',
        'total',
        'balance',
        'refunded',
        'paid_usd',
        'paid_kh',
    ];

    protected $casts = [
        'rate' => 'float',
        'discount_invoice' => 'float',
        'status' => 'integer',
        'date' => 'datetime',
        'term' => 'boolean',
        'show_vattin' => 'boolean',
        'invoice_id' => 'integer',
        'invoice_num_year_series' => 'integer',
        'prefix_id' => 'integer',
        'total' => 'float',
        'balance' => 'float',
        'refunded' => 'float',
        'paid_usd' => 'float',
        'paid_kh' => 'float',
    ];

    protected $appends = [
        'invoice_number_formatted',
        'status_label',
        'patient_name',
        'doctor_name',
        'assistant_name',
    ];

    public static function statusConf()
    {
        return [
            self::PARTIAL           => ['info', 'Partial'],
            self::UNPAID            => ['danger', 'Unpaid'],
            self::PAID              => ['success', 'Paid'],
            self::REFUNED           => ['purple', 'Refunded'],
            self::PARTIALLY_REFUNED => ['indigo', 'Partially Refunded'],
        ];
    }

    public static function statusLists()
    {
        return collect(self::statusConf())->map(function ($item) {
            return lang($item[1] ?? $item[0]);
        });
    }

    /**
     * Determine invoice status integer code based on financial numbers.
     */
    public static function determineStatus(float $total, float $paidUsd, float $refunded, float $balance): int
    {
        if ($refunded > 0.001) {
            if ($paidUsd > 0.001 && $refunded < $paidUsd) {
                return self::PARTIALLY_REFUNED; // 5
            }
            return self::REFUNED; // 4
        }

        if ($total > 0 && $balance <= 0.001) {
            return self::PAID; // 3
        }

        if ($paidUsd > 0.001 && $balance > 0.001) {
            return self::PARTIAL; // 1
        }

        return self::UNPAID; // 2
    }

    /**
     * Get label for an integer status code.
     */
    public static function statusLabel(int $status): string
    {
        return match ($status) {
            self::PARTIAL => 'Partial',
            self::UNPAID => 'Unpaid',
            self::PAID => 'Paid',
            self::REFUNED => 'Refunded',
            self::PARTIALLY_REFUNED => 'Partially Refunded',
            default => 'Unpaid',
        };
    } 

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'user_id');
    }

    public function prefix(): BelongsTo
    {
        return $this->belongsTo(Prefix::class, 'prefix_id');
    }

    public function medicalDetails(): HasMany
    {
        return $this->hasMany(MedicalDetail::class, 'medical_history_id');
    }

    public function treatmentTeeths(): HasMany
    {
        return $this->hasMany(TreatmentTeeth::class, 'medical_history_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'medical_history_id');
    }

    public function receptions(): HasMany
    {
        return $this->hasMany(Reception::class, 'invoice_id');
    }

    public function getInvoiceNumberFormattedAttribute(): string
    {
        if (!empty($this->attributes['invoice_number'])) {
            return $this->attributes['invoice_number'];
        }

        $prefix = $this->prefix_invoice ?: 'INV';
        if ($this->prefix_format === 'Year-Series' || ($this->invoice_num_year_series && !$this->invoice_id)) {
            $year = $this->date ? Carbon::parse($this->date)->format('Y') : date('Y');
            $num = $this->invoice_num_year_series ?: $this->invoice_id ?: $this->id;
            return sprintf('%s-%s-%06d', $prefix, $year, $num);
        }

        $num = $this->invoice_id ?: $this->id;
        return sprintf('%s-%06d', $prefix, $num);
    }

    public function getStatusLabelAttribute(): string
    {
        $statusInt = (int) $this->status;

        if (array_key_exists($statusInt, self::statusConf())) {
            return self::statusConf()[$statusInt][1];
        }

        // Fallback calculation if status column is null or custom
        $balance = (float) ($this->attributes['balance'] ?? 0);
        $total = (float) ($this->attributes['total'] ?? 0);
        $paid = (float) ($this->attributes['paid_usd'] ?? 0);
        $refunded = (float) ($this->attributes['refunded'] ?? 0);

        $computedStatus = self::determineStatus($total, $paid, $refunded, $balance);
        return self::statusLabel($computedStatus);
    }

    public function getPatientNameAttribute(): string
    {
        if ($this->relationLoaded('patient') && $this->patient) {
            return trim(($this->patient->firstname ?? '') . ' ' . ($this->patient->lastname ?? ''));
        }
        return '';
    }

    public function getDoctorNameAttribute(): string
    {
        if ($this->relationLoaded('doctor') && $this->doctor) {
            return $this->doctor->full_name ?? '';
        }
        return '';
    }

    public function getAssistantNameAttribute(): string
    {
        if ($this->relationLoaded('assistant') && $this->assistant) {
            if ($this->assistant->relationLoaded('profile') && $this->assistant->profile) {
                return $this->assistant->profile->full_name ?: ($this->assistant->name ?? '');
            }
            return $this->assistant->name ?? '';
        }
        return '';
    }
}
