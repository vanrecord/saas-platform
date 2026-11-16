<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PaymentMethod extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'payment_methods';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'name',
        'description',
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
     * Register Spatie media collection.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('qrcode')
            ->useDisk('digitalocean')
            ->singleFile();
    }

    /**
     * Get the public URL for the QR code image.
     */
    public function getQrCodeUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('qrcode');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Relationship to user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'user_id');
    }

    /**
     * Relationship to payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_method_id', 'id');
    }

    /**
     * Activity description helper.
     */
    public function activityDescription($event, $user = null): string
    {
        $userName = $user && method_exists($user, 'profile') && $user->profile
            ? ($user->profile->fullname ?? $user->name)
            : ($user->name ?? 'User');

        return 'ExpenseCategory ' . $this->name . ' has been ' . $event . ' by ' . $userName;
    }

    /**
     * Find payment method by ID with decoded datas.
     */
    public function findPaymentMethodByID($bank_id): ?self
    {
        $bank = self::find($bank_id);
        if (!$bank) {
            return null;
        }

        if (is_array($bank->datas)) {
            return $bank;
        }

        $bank->datas = !empty($bank->datas)
            ? (is_string($bank->datas) ? (json_decode($bank->datas, true) ?: []) : $bank->datas)
            : [];

        return $bank;
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
     * Scope for active payment methods.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_disable', false);
    }

    /**
     * Scope for active banks / methods with integration data.
     */
    public function scopeActiveBanks(Builder $query): Builder
    {
        return $query->where('is_disable', false)
            ->whereNotNull('datas')
            ->where('datas', '!=', '')
            ->where('datas', '!=', '[]')
            ->where('datas', '!=', '{}');
    }

    /**
     * Check if payment method has valid, non-empty ABA integration data.
     */
    public function hasValidAbaData(): bool
    {
        if ($this->is_disable) {
            return false;
        }

        $datas = $this->datas;
        if (is_string($datas)) {
            $datas = json_decode($datas, true);
        }

        if (empty($datas) || !is_array($datas)) {
            return false;
        }

        // Must have at least one non-empty value (e.g. merchant_id or api_key)
        $filtered = array_filter($datas, function ($val) {
            return !is_null($val) && $val !== '';
        });

        return count($filtered) > 0;
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
