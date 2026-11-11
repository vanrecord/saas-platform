<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentTeeth extends Model
{
    use HasFactory;

    protected $table = 'treatment_teeths';

    protected $fillable = [
        'medical_history_id',
        'medical_detail_id',
        'teeth_number',
        'class_one',
        'class_two',
        'class_three',
        'class_four',
        'class_five',
        'class_six',
        'tooth_type',
        'tooth_status',
    ];

    protected $casts = [
        'class_one' => 'boolean',
        'class_two' => 'boolean',
        'class_three' => 'boolean',
        'class_four' => 'boolean',
        'class_five' => 'boolean',
        'class_six' => 'boolean',
    ];

    public function medicalHistory(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class, 'medical_history_id');
    }

    public function medicalDetail(): BelongsTo
    {
        return $this->belongsTo(MedicalDetail::class, 'medical_detail_id');
    }

    public function toothFindings(): HasMany
    {
        return $this->hasMany(ToothFinding::class, 'treatment_teeth_id');
    }
}
