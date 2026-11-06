<?php

namespace App\Models;

use App\Models\CoreBase;
use App\Models\Domain;
use App\Models\User;
use App\Supports\Trait\CoreTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sector extends CoreBase
{
    use HasFactory;
    use SoftDeletes;
    use CoreTrait;

    protected $fillable = [
        'code',
        'name_native',
        'name_international',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function createdBy(){
       
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy(){
       
        return $this->belongsTo(User::class,'updated_by');
    }
    public function domains(){
        return $this->hasMany(Domain::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeActiveOrCurrentId($query, $id = null)
    {
        return $query->where('is_active', true)
                ->when($id, function ($q) use ($id) {
                    $q->orWhere('id', $id);
                });
    }
}
