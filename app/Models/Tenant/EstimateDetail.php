<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstimateDetail extends Model
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

    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $table = 'estimate_details';

    protected $fillable = [
        'estimate_id',
        'detailable_id',
        'detailable_type',
        'service_id',
        'date',
        'quantity',
        'unit_price',
        'cost',
        'cost_amount',
        'discount',
        'discount_as',
        'amount',
        'note',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejected_note',
        'medical_detail_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'cost' => 'float',
        'cost_amount' => 'float',
        'discount' => 'float',
        'amount' => 'float',
        'status' => 'integer',
        'date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'estimate_id' => 'integer',
        'service_id' => 'integer',
        'medical_detail_id' => 'integer',
        'approved_by' => 'integer',
        'rejected_by' => 'integer',
    ];

    protected $appends = [
        'item_name',
        'item_type',
        'status_label',
        'teeth_numbers',
    ];

    public static function statusLabels(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::APPROVED => 'Approved',
            self::REJECTE => 'Rejected',
            self::COMPLETED => 'Completed',
            self::VERIFIED => 'Verified',
            self::PARTIALLY_APPROVED => 'Partially Approved',
            self::PARTIALLY_REJECTED => 'Partially Rejected',
        ];
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class, 'estimate_id');
    }

    public function detailable(): MorphTo
    {
        return $this->morphTo();
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function medicalDetail(): BelongsTo
    {
        return $this->belongsTo(MedicalDetail::class, 'medical_detail_id');
    }

    public function teeth(): HasMany
    {
        return $this->hasMany(EstimateTooth::class, 'estimate_detail_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'approved_by');
    }

    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'rejected_by');
    }

    public function getItemNameAttribute(): string
    {
        if ($this->relationLoaded('detailable') && $this->detailable) {
            return $this->detailable->name ?? '';
        }
        if ($this->relationLoaded('service') && $this->service) {
            return $this->service->name ?? '';
        }
        if ($this->service_id) {
            return Service::where('id', $this->service_id)->value('name') ?? '';
        }
        if ($this->detailable_id && $this->detailable_type) {
            $class = $this->detailable_type;
            if (class_exists($class)) {
                return $class::where('id', $this->detailable_id)->value('name') ?? '';
            }
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

    public function getStatusLabelAttribute(): string
    {
        $status = $this->status ?? self::DRAFT;
        $labels = self::statusLabels();
        return $labels[$status] ?? 'Draft';
    }

    public function getTeethNumbersAttribute(): array
    {
        if ($this->relationLoaded('teeth')) {
            return $this->teeth->pluck('teeth_number')->filter()->values()->toArray();
        }
        return $this->teeth()->pluck('teeth_number')->filter()->values()->toArray();
    }
}
