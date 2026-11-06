<?php
namespace App\Supports\Trait;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


trait CoreTrait
{

   use LogsActivity;
    // use SoftDeletes;
    protected $defaultPerPage =10;
    public function getPerPage()
    {
       $perPage = app('request')->get('perPage');
       return $perPage?$perPage:$this->defaultPerPage;
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll()
        ->logOnlyDirty()
        ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
}