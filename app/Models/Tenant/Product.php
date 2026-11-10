<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'hospital_id',
        'supplier_id',
        'category_id',
        'code',
        'name',
        'price',
        'cost',
        'qty',
        'status',
        'description',
    ];

    protected $casts = [
        'price' => 'float',
        'cost' => 'float',
        'qty' => 'integer',
        'status' => 'boolean',
        'code' => 'integer',
        'supplier_id' => 'integer',
        'category_id' => 'integer',
        'hospital_id' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->code)) {
                $product->code = static::getNextCode();
            }
        });
    }

    public static function getNextCode(): int
    {
        $maxCode = static::max('code');
        $maxId = static::max('id');
        $max = max($maxCode ?? 0, $maxId ?? 0);
        return max($max + 1, 1001);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }
}
