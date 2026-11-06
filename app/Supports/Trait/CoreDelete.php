<?php
namespace App\Supports\Trait;

trait CoreDelete
{
    public static function boot()
    {
        parent::boot();
        
        static::deleting(function ($model){
            $model->deleted_by = auth()->user()?->id;
        });
    }
}