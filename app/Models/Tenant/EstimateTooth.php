<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstimateTooth extends Model
{
    use HasFactory;

    protected $table = 'estimate_teeths';

    protected $fillable = [
        'estimate_detail_id',
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
        'estimate_detail_id' => 'integer',
    ];

    protected $appends = [
        'tooth_findings',
    ];

    public function getToothFindingsAttribute()
    {
        return $this->relationLoaded('toothFindings') ? $this->toothFindings : $this->toothFindings()->get();
    }

    public function estimateDetail(): BelongsTo
    {
        return $this->belongsTo(EstimateDetail::class, 'estimate_detail_id');
    }

    public function toothFindings(): HasMany
    {
        return $this->hasMany(EstimateToothFinding::class, 'estimate_teeth_id');
    }
}
