<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DentalCondition extends Model
{
    use HasFactory;

    protected $table = 'dental_conditions';

    protected $fillable = [
        'name',
        'code',
        'description',
        'default_color',
        'category',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function findings(): HasMany
    {
        return $this->hasMany(ToothFinding::class, 'dental_condition_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'created_by');
    }
}

