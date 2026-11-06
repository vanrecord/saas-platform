<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $fillable = [
        'code',
        'name_kh',
        'name_en',
        'type',
        'reference',
        'official_note',
        'commune_id'
    ];

    protected $defaultPerPage =10;
    public function getPerPage()
    {
       $perPage = app('request')->get('perPage');
       return $perPage?$perPage:$this->defaultPerPage;
    }

    public function commune()
    {
        return $this->belongsTo('App\Models\Commune');
    }
}