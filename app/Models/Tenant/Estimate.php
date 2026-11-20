<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estimate extends Model
{
    use HasFactory, SoftDeletes;

    const DRAFT     = 1;
    const APPROVED  = 2;
    const REJECTE   = 3;
    const REJECTED  = 3;
    const COMPLETED = 4;
    const VERIFIED  = 5;
    const PARTIALLY_APPROVED = 6;
    const PARTIALLY_REJECTED = 7;

    protected $table = 'estimates';

    protected $fillable = [
        'doctor_id',
        'user_id',
        'patient_id',
        'diagnosis',
        'medical_alert',
        'description',
        'reason',
        'rate',
        'discount_invoice',
        'estimate_at',
        'approved_at',
        'submitted_at',
        'closed_at',
        'is_completed',
        'status',
        'employee_id',
        'term',
        'approved_by',
        'created_by',
        'closed_by',
        'rejected_by',
        'code',
        'discount_inv_as',
        'chief_complaint',
    ];

    protected $casts = [
        'rate' => 'float',
        'discount_invoice' => 'float',
        'status' => 'integer',
        'estimate_at' => 'datetime',
        'approved_at' => 'datetime',
        'submitted_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_completed' => 'boolean',
        'term' => 'boolean',
        'code' => 'integer',
        'doctor_id' => 'integer',
        'patient_id' => 'integer',
        'user_id' => 'integer',
        'employee_id' => 'integer',
        'approved_by' => 'integer',
        'created_by' => 'integer',
        'closed_by' => 'integer',
        'rejected_by' => 'integer',
    ];

    protected $appends = [
        'quote_no',
        'status_label',
        'patient_name',
        'patient_id_number',
        'doctor_name',
        'assistant_name',
        'subtotal',
        'discount_amount',
        'total',
        'balance',
        'balance_kh',
        'paid_usd',
        'paid_kh',
    ];

    public static function statusOptions(): array
    {
        return [
            self::DRAFT => ['label' => 'Draft', 'badge' => 'gray', 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'],
            self::APPROVED => ['label' => 'Approved', 'badge' => 'success', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300'],
            self::REJECTE => ['label' => 'Rejected', 'badge' => 'danger', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300'],
            self::COMPLETED => ['label' => 'Completed', 'badge' => 'primary', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'],
            self::VERIFIED => ['label' => 'Verified', 'badge' => 'purple', 'class' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300'],
            self::PARTIALLY_APPROVED => ['label' => 'Partially Approved', 'badge' => 'info', 'class' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300'],
            self::PARTIALLY_REJECTED => ['label' => 'Partially Rejected', 'badge' => 'warning', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'],
        ];
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
        return $this->belongsTo(UserTenant::class, 'employee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'user_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'approved_by');
    }

    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'rejected_by');
    }

    public function estimateDetails(): HasMany
    {
        return $this->hasMany(EstimateDetail::class, 'estimate_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(MedicalHistory::class, 'estimate_id');
    }

    public function getQuoteNoAttribute(): string
    {
        $code = $this->code ?: $this->id;
        return sprintf('QUT%06d', $code);
    }

    public function getStatusLabelAttribute(): string
    {
        $status = $this->status ?? self::DRAFT;
        $options = self::statusOptions();
        return $options[$status]['label'] ?? 'Draft';
    }

    public function getPatientNameAttribute(): string
    {
        if ($this->relationLoaded('patient') && $this->patient) {
            return $this->patient->full_name ?? ($this->patient->name ?? 'N/A');
        }
        return $this->patient()->value('firstname') ? trim($this->patient()->value('firstname') . ' ' . $this->patient()->value('lastname')) : 'N/A';
    }

    public function getPatientIdNumberAttribute(): string
    {
        if ($this->relationLoaded('patient') && $this->patient) {
            return (string) ($this->patient->id_number ?: $this->patient->id);
        }
        $idNumber = $this->patient()->value('id_number');
        return (string) ($idNumber ?: $this->patient_id);
    }

    public function getDoctorNameAttribute(): string
    {
        if ($this->relationLoaded('doctor') && $this->doctor) {
            return $this->doctor->full_name ?: ($this->doctor->user?->name ?? '');
        }
        return '';
    }

    public function getAssistantNameAttribute(): string
    {
        if ($this->relationLoaded('assistant') && $this->assistant) {
            return $this->assistant->name ?? '';
        }
        return '';
    }

    public function getSubtotalAttribute(): float
    {
        if ($this->relationLoaded('estimateDetails')) {
            return (float) $this->estimateDetails->sum('amount');
        }
        return (float) $this->estimateDetails()->sum('amount');
    }

    public function getDiscountAmountAttribute(): float
    {
        $subtotal = $this->subtotal;
        $discVal = (float) ($this->discount_invoice ?? 0);
        $discAs = $this->discount_inv_as ?? '%';

        if ($discAs === '%') {
            $amount = $subtotal * ($discVal / 100);
        } else {
            $amount = $discVal;
        }

        return (float) min($subtotal, max(0, $amount));
    }

    public function getTotalAttribute(): float
    {
        return (float) max(0, $this->subtotal - $this->discount_amount);
    }

    public function getPaidUsdAttribute(): float
    {
        return 0.0;
    }

    public function getPaidKhAttribute(): float
    {
        return 0.0;
    }

    public function getBalanceAttribute(): float
    {
        return $this->total;
    }

    public function getBalanceKhAttribute(): float
    {
        $rate = (float) ($this->rate ?: 4050);
        return (float) round($this->balance * $rate, 2);
    }

    /**
     * Recalculates and updates the overall quote status from its items.
     * Preserves COMPLETED and VERIFIED statuses during normal item processing.
     */
    public function recalculateStatus(): int
    {
        // Do not overwrite COMPLETED or VERIFIED statuses during normal item processing
        if (in_array((int) $this->status, [self::COMPLETED, self::VERIFIED])) {
            return (int) $this->status;
        }

        $items = $this->relationLoaded('estimateDetails') ? $this->estimateDetails : $this->estimateDetails()->get();
        $totalItems = $items->count();

        if ($totalItems === 0) {
            $newStatus = self::DRAFT;
        } else {
            $approvedCount = $items->where('status', self::APPROVED)->count();
            $rejectedCount = $items->whereIn('status', [self::REJECTE, self::REJECTED])->count();
            $draftCount = $items->where('status', self::DRAFT)->count();

            if ($approvedCount === $totalItems) {
                $newStatus = self::APPROVED;
            } elseif ($rejectedCount === $totalItems) {
                $newStatus = self::REJECTE;
            } elseif ($rejectedCount > 0 && ($draftCount > 0 || $approvedCount > 0)) {
                $newStatus = self::PARTIALLY_REJECTED;
            } elseif ($approvedCount > 0 && $draftCount > 0) {
                $newStatus = self::PARTIALLY_APPROVED;
            } else {
                $newStatus = self::DRAFT;
            }
        }

        $this->status = $newStatus;

        if ($this->exists) {
            $updateData = ['status' => $newStatus];
            if ($newStatus === self::APPROVED) {
                $updateData['is_completed'] = true;
                $updateData['approved_at'] = Carbon::now();
                $updateData['approved_by'] = auth()->id() ?: $this->approved_by;
            }
            $this->update($updateData);
        }

        return $newStatus;
    }
}
