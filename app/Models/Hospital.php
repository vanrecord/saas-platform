<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Addressable;

class Hospital extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospitals';

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'user_id');
    }
    public function addressable()
    {
        return $this->morphOne(Addressable::class, 'addressable');
    }
}
