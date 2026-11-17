<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionDetail extends Model
{
    use HasFactory;

    protected $table = 'prescription_details';

    protected $fillable = [
        'medicine_id',
        'prescription_id',
        'dose',
        'duration',
        'dose_amount',
        'morning',
        'afternoon',
        'evening',
        'night',
        'note',
        'unit_dose',
    ];

    protected $casts = [
        'medicine_id' => 'integer',
        'prescription_id' => 'integer',
        'dose' => 'float',
        'duration' => 'float',
        'dose_amount' => 'float',
        'morning' => 'boolean',
        'afternoon' => 'boolean',
        'evening' => 'boolean',
        'night' => 'boolean',
        'unit_dose' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'total_calculated',
        'usage_text',
        'summary_text',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }

    public function getTotalCalculatedAttribute(): float
    {
        $times = 0;
        if ($this->morning) $times++;
        if ($this->afternoon) $times++;
        if ($this->evening) $times++;
        if ($this->night) $times++;

        $qty = (float) ($this->dose_amount ?: 1);
        $duration = (float) ($this->duration ?: ($this->unit_dose ?: 1));

        return (float) ($qty * ($times > 0 ? $times : 1) * $duration);
    }

    public function getUsageTextAttribute(): string
    {
        $parts = [];
        $qtyStr = (string) (int) ($this->dose_amount ?: 1);

        if ($this->morning) {
            $parts[] = 'ព្រឹក' . $qtyStr;
        }
        if ($this->afternoon) {
            $parts[] = 'ថ្ងៃ' . $qtyStr;
        }
        if ($this->evening) {
            $parts[] = 'ល្ងាច' . $qtyStr;
        }
        if ($this->night) {
            $parts[] = 'យប់' . $qtyStr;
        }

        return !empty($parts) ? implode(' ', $parts) : '-';
    }

    public function getSummaryTextAttribute(): string
    {
        $medName = $this->medicine?->name ?: ('Medicine #' . $this->medicine_id);
        $total = (int) $this->total_calculated;
        return "{$medName} ({$total} គ្រាប់)";
    }
}
