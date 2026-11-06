<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use App\Supports\Trait\LogsApi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogAccessRequestApi extends Model
{
    use HasFactory, LogsApi;

    const ACTION_REQUEST    = 'request';
    const ACTION_ACCESS     = 'access';

    protected $table = "log_access_request_apis";

    protected $fillable = [
        'description',
        'request_method',
    	'request_url',
    	'request_param',
    	'response_data',
    	'response_message',
    	'causer_id',
    	'causer_type',
    ];
    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected $defaultPerPage =10;
    
    public function getPerPage()
    {
       $perPage = app('request')->get('perPage');
       return $perPage?$perPage:$this->defaultPerPage;
    }

    public function causerId(){

        return $this->belongsTo(User::class,'causer_id');
    }

    protected function createdAt():Attribute
    {
        return Attribute::make(
            set: fn ($value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
            get: fn ($value) => Carbon::parse($value)->format('d/m/Y H:i')
        );

    }

}
