<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prefix extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prefixs';

    protected $fillable = [
        'prefix_name',
        'prefix_format',
        'default_prefix',
    ];

    protected $casts = [
        'default_prefix' => 'boolean',
    ];

    public function scopeDefault($query)
    {
        return $query->where('default_prefix', true);
    }
}
