<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\CoreBase;
use Stancl\VirtualColumn\VirtualColumn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Settings\PrefixSettings;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptTenant extends CoreBase
{
    use HasFactory;
    use VirtualColumn;
    protected $guarded = [];
    
    protected $table = 'receipts';
    protected $appends = [
        'start_date_utc',
        'end_date_utc',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'receiptable_id',
            'receiptable_type',
            'plan_name',
            'code',
            'paid_by',
            'paid_date',
            'total',
            'payment_type',
            'unit_price',
            'discount',
            'discount_type',
            'currency',
            'currency_symbol',
            'qty',
            'start_date',
            'end_date',
            'billing_interval',
            'features',
            'is_bank',
            'created_by',
            'updated_by',
            'period',
            'period_type',
            'prefix',
            'year_series_code'
        ];
    }
    public static function getDataColumn(): string
    {
        return 'data';
    }

    const CASH = 'cash';

    // billing_interval have value ['monthly', 'yearly', 'quarterly']
    public function receiptable()
    {
        return $this->morphTo();
    }
    public static function booted()
    {   
        static::creating(function ($receipt) {
            if(empty($receipt->isDirty('code')) && empty($receipt->isDirty('year_series_code')) && empty($receipt->isDirty('prefix'))){
                $receipt->year_series_code = Receipt::yearSeriesCode();
                $receipt->code = app(Receipt::class)->max('code')+1;
                $receipt->prefix = get_prefix_code('prefix_receipt');
            }
        });
    }
    public static function yearSeriesCode()
    {
        $yearSeriesCode = app(Receipt::class)->whereYear('paid_date','=',Date('Y'))->max('year_series_code')+1;
        return $yearSeriesCode;
    }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by');
    }

    public function paidBy(){
        return $this->belongsTo(User::class,'paid_by');
    }

    public function scopeIsValid(Builder $query): Builder
    {
        return $query->where('end_date', '>=', now());
    }

    protected function startDateUtc(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::parse($this->start_date, config('app.timezone'))
                ->setTimezone('UTC'),
        );
    }

    protected function endDateUtc(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::parse($this->end_date, config('app.timezone'))
                ->setTimezone('UTC'),
        );
    }
}
