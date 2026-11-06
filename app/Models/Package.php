<?php

namespace App\Models;

use App\Models\User;
use App\Models\Domain;
use App\Models\Module;
use App\Supports\Trait\CoreDelete;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends CoreBase
{
    use HasFactory, SoftDeletes, CoreDelete;
    protected $table = 'packages';
    protected $fillable = [
        'name',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
        'updated_at'
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class,'module_has_package', 'package_id','module_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public  function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeActiveOrCurrentId($query, $id)
    {
        return $query->active()->orWhere('id', $id);
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

}
