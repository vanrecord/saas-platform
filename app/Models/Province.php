<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'code',
        'name_kh',
        'name_en',
        'calling_code',
        'prefix_code',
        'province_type_kh',
        'province_type_en',
        'description',
        'created_at',
        'updated_at'
    ];
    
    protected $defaultPerPage =10;
    public function getPerPage()
    {
       $perPage = app('request')->get('perPage');
       return $perPage?$perPage:$this->defaultPerPage;
    }
    public function districts()
    {
        return $this->hasMany('App\Models\District');
    }
}