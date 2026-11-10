<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'hospital_id',
        'name',
        'description',
        'type',
        'is_active',
        'code',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'updated_by');
    }

    public function scopeServices(Builder $query): Builder
    {
        return $query->where('type', 'service');
    }

    public function scopeProducts(Builder $query): Builder
    {
        return $query->where('type', 'product');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
