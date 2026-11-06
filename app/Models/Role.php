<?php

namespace App\Models;

use App\Supports\Trait\CoreTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use \Spatie\Permission\Models\Role as BaseRole;
use DateTimeInterface;

class Role extends BaseRole
{

    use CoreTrait;
    use HasFactory;
    
    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i'
    ];
    protected $appends = [
        'disabled_btn'
    ];
    const IS_OWNER = 'owner';
    public function getDisabledBtnAttribute($value)
    {
        if($this->name==="owner"){
            return true;
        }
        return false;
    }
    public static function booted()
    {
        static::creating(function ($model) {
            $model->updated_by = auth()->user()?->id;
            $model->created_by = auth()->user()?->id;
        });
        static::updating(function ($model) {
            $model->updated_by = auth()->user()?->id;
        });
    }
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function createdBy(){
       
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy(){
       
        return $this->belongsTo(User::class,'updated_by');
    }
   
}
