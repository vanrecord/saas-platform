<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class DentalChart extends Model
{
    use HasFactory;

    protected $table = 'dental_charts';

    protected $fillable = [
        'patient_id',
        'invoice_id',
        'chart_date',
        'dentition_type',
        'examination_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'chart_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function teeth(): HasMany
    {
        return $this->hasMany(ChartTooth::class, 'dental_chart_id');
    }

    public function findings(): HasManyThrough
    {
        return $this->hasManyThrough(ToothFinding::class, ChartTooth::class, 'dental_chart_id', 'chart_tooth_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'updated_by');
    }
}

