<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $fillable = [
        'code',
        'name_kh',
        'name_en',
        'type',
        'reference',
        'official_note',
        'description',
        'district_id',
    ];

    protected $defaultPerPage =10;
    public function getPerPage()
    {
       $perPage = app('request')->get('perPage');
       return $perPage?$perPage:$this->defaultPerPage;
    }

    public function district()
    {
        return $this->belongsTo('App\Models\District');
    }

    public function villages()
    {
        return $this->hasMany('App\Models\Village');
    }
}