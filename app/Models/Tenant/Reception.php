<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reception extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'receptions';

    protected $fillable = [
        'reception_number',
        'patient_id',
        'invoice_id',
        'doctor_id',
        'reception_date',
        'visit_type',
        'chief_complaint',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'reception_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class, 'invoice_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'created_by');
    }

    public static function generateReceptionNumber(): string
    {
        $datePrefix = date('Ymd');
        $latest = self::where('reception_number', 'like', "REC-{$datePrefix}-%")
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($latest && preg_match('/REC-\d+-(\d+)/', $latest->reception_number, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return sprintf('REC-%s-%04d', $datePrefix, $sequence);
    }
}
