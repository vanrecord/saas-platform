<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'code',
        'name_kh',
        'name_en',
        'type',
        'reference',
        'official_note',
        'description',
        'province_id'
    ];
    
    protected $defaultPerPage =10;
    public function getPerPage()
    {
       $perPage = app('request')->get('perPage');
       return $perPage?$perPage:$this->defaultPerPage;
    }
    public function communes()
    {
        return $this->hasMany('App\Models\Commune');
    }
    public function province()
    {
        return $this->belongsTo('App\Models\Province');
    }

}