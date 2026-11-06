<?php 
namespace App\Models;

use DateTimeInterface;
use App\Supports\Trait\CoreTrait;
use Illuminate\Database\Eloquent\Model;

abstract class CoreBase extends Model
{
    use  CoreTrait;

    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
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

        static::deleted(function ($model){
            if($model->deleted_by){
                $model->deleted_by = auth()->user()?->id;
                $model->save();
            }
        });
    }

}