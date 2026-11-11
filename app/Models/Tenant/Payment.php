<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'patient_id',
        'medical_history_id',
        'date',
        'amount',
        'amount_kh',
        'payment_method_id',
        'user_id',
        'credit_balance',
        'deposit_balance',
        'txnref',
        'currency',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'float',
        'amount_kh' => 'integer',
        'credit_balance' => 'float',
        'deposit_balance' => 'float',
    ];

    protected $appends = [
        'patient_name',
        'invoice_number',
        'payment_method_name',
        'verified_by_name',
    ];

    public function medicalHistory(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class, 'medical_history_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'user_id');
    }

    public function getPatientNameAttribute(): string
    {
        if (!$this->patient) {
            return '-';
        }
        $name = trim(($this->patient->firstname ?? '') . ' ' . ($this->patient->lastname ?? ''));
        return $name ?: ($this->patient->id_number ? 'Patient #' . $this->patient->id_number : 'Patient #' . $this->patient->id);
    }

    public function getInvoiceNumberAttribute(): string
    {
        if ($this->medicalHistory) {
            return $this->medicalHistory->invoice_number_formatted ?: ($this->medicalHistory->invoice_number ?: '#' . $this->medicalHistory->id);
        }
        return $this->medical_history_id ? '#' . $this->medical_history_id : '-';
    }

    public function getPaymentMethodNameAttribute(): string
    {
        return $this->paymentMethod?->name ?: '-';
    }

    public function getVerifiedByNameAttribute(): string
    {
        if (!$this->verifiedBy) {
            return '-';
        }
        return $this->verifiedBy->profile?->full_name ?: ($this->verifiedBy->name ?: 'User #' . $this->verifiedBy->id);
    }
}
