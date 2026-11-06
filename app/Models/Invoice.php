<?php

namespace App\Models;

use App\Models\Tenant;
use App\Models\Subscription;
use App\Supports\Trait\CoreTrait;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use CoreTrait;
    protected $table = 'invoices';

    protected $fillable = [
        'subscription_id',
        'code',
        'total',
        'unit_price',
        'currency',
        'currency_symbol',
        'discount',
        'discount_type',
        'paid_by',
        'paid_at',
        'paid_name',
        'payment_type',
        'qty',
        'invoiced_at',
        'tenant_id',
        'prefix',
        'year_series_code',
        'is_bank',
        'created_by',
        'updated_by',
        'period',
        'period_type',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function booted()
    {   
        static::creating(function ($receipt) {
            if(empty($receipt->isDirty('code')) && empty($receipt->isDirty('year_series_code'))){
                $receipt->year_series_code = Invoice::yearSeriesCode();
                $receipt->code = app(Invoice::class)->max('code')+1;
            }
        });
    }
    public static function yearSeriesCode()
    {
        $yearSeriesCode = app(Invoice::class)->whereYear('invoiced_at','=',Date('Y'))->max('year_series_code')+1;
        return $yearSeriesCode;
    }

}
