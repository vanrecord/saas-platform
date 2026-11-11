<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medical_details';

    protected $fillable = [
        'medical_history_id',
        'detailable_id',
        'detailable_type',
        'quantity',
        'unit_price',
        'discount',
        'date',
        'service_id',
        'amount',
        'note',
        'discount_as',
        'cost',
        'cost_amount',
        'is_lock',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'discount' => 'float',
        'amount' => 'float',
        'cost' => 'float',
        'cost_amount' => 'float',
        'is_lock' => 'boolean',
        'date' => 'date',
    ];

    protected $appends = [
        'item_name',
        'item_type',
    ];

    public function medicalHistory(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class, 'medical_history_id');
    }

    public function detailable(): MorphTo
    {
        return $this->morphTo();
    }

    public function treatmentTeeth(): HasMany
    {
        return $this->hasMany(TreatmentTeeth::class, 'medical_detail_id');
    }

    public function getItemNameAttribute(): string
    {
        if ($this->relationLoaded('detailable') && $this->detailable) {
            return $this->detailable->name ?? '';
        }
        return '';
    }

    public function getItemTypeAttribute(): string
    {
        if ($this->detailable_type === Product::class || str_contains((string) $this->detailable_type, 'Product')) {
            return 'product';
        }
        return 'service';
    }
}
