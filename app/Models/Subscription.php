<?php

namespace App\Models;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan_name',
        'total',
        'unit_price',
        'discount',
        'discount_type',
        'currency',
        'currency_symbol',
        'qty',
        'tenant_id',
        'start_date',
        'end_date',
        'features',
        'status',
        'period',
        'period_type',
        'data',
        'ehealth_code',
        'is_connected_ehealth',
        'is_deleted_ehealth',
    ];

    const ACTIVED    = 'actived';
    const Expired   = 'expired';
    const CANCELLED = 'cancelled';
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class);
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

}
