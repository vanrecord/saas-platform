<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'qr_code',
        'print',
        'is_sponsor',
        'provider',
        'order',
        'datas',
        'register_ref',
        'currency',
        'partner_name',
        'is_disable',
        'is_activate',
        'is_default_payment',
    ];

    protected $casts = [
        'print' => 'boolean',
        'is_sponsor' => 'boolean',
        'is_disable' => 'boolean',
        'is_activate' => 'boolean',
        'is_default_payment' => 'boolean',
        'datas' => 'array',
        'order' => 'integer',
    ];

    protected $appends = [
        'qr_code_url',
        'safe_datas',
    ];

    /**
     * Get the public URL for the QR code image.
     */
    public function getQrCodeUrlAttribute(): ?string
    {
        $qrCode = $this->attributes['qr_code'] ?? null;
        if (empty($qrCode)) {
            return null;
        }

        if (filter_var($qrCode, FILTER_VALIDATE_URL)) {
            return $qrCode;
        }

        return Storage::disk('public')->url($qrCode);
    }

    /**
     * Get provider configuration safe for frontend exposure (without secrets).
     */
    public function getSafeDatasAttribute(): array
    {
        $datas = $this->datas;
        if (is_string($datas)) {
            $datas = json_decode($datas, true) ?: [];
        } elseif (!is_array($datas)) {
            $datas = [];
        }

        $safe = $datas;
        if (!empty($safe['api_key'])) {
            $safe['has_api_key'] = true;
            $safe['api_key'] = '••••••••';
        } else {
            $safe['has_api_key'] = false;
            $safe['api_key'] = '';
        }

        return $safe;
    }

    /**
     * Relationship to payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_method_id');
    }

    /**
     * Scope for active payment methods.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_disable', false);
    }

    /**
     * Scope for methods with online payment enabled.
     */
    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('is_activate', true)->where('is_disable', false);
    }

    /**
     * Scope for default payment method.
     */
    public function scopeDefaultPayment(Builder $query): Builder
    {
        return $query->where('is_default_payment', true);
    }

    /**
     * Scope for default print payment method.
     */
    public function scopeDefaultPrint(Builder $query): Builder
    {
        return $query->where('print', true);
    }
}
