<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundTransfer extends Model
{
    use HasFactory;

    protected $table = 'fund_transfers';

    ### --- STATUS CONSTANTS --- ###
    const DRAFT    = 0;
    const VERIFIED = 1;

    protected $fillable = [
        'code',
        'client_id',
        'patient_id',
        'status',
        'amount',
        'base_amount',
        'currency',
        'rate',
        'created_by',
        'verify_by',
        'note',
        'is_cash',
        'is_verify',
        'date',
        'is_ehealth',
    ];

    protected $casts = [
        'code' => 'integer',
        'client_id' => 'integer',
        'patient_id' => 'integer',
        'status' => 'integer',
        'amount' => 'float',
        'base_amount' => 'float',
        'rate' => 'float',
        'created_by' => 'integer',
        'verify_by' => 'integer',
        'is_cash' => 'boolean',
        'is_verify' => 'boolean',
        'is_ehealth' => 'boolean',
        'date' => 'datetime',
    ];

    protected $appends = [
        'formatted_code',
        'date_formatted',
        'status_label',
        'amount_formatted',
    ];

    public static function statusConf(): array
    {
        return [
            self::DRAFT => ['default', 'Draft'],
            self::VERIFIED => ['success', 'Verified'],
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                if (empty($model->created_by)) {
                    $model->created_by = auth()->id();
                }
                if (empty($model->client_id)) {
                    $user = auth()->user();
                    $model->client_id = $user->hospital_id ?? ($user->client_id ?? null);
                }
            }
        });
    }

    public function setDateAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['date'] = null;
            return;
        }

        if ($value instanceof Carbon || $value instanceof \DateTimeInterface) {
            $this->attributes['date'] = $value;
            return;
        }

        try {
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $value)) {
                $format = strlen($value) > 10 ? 'd/m/Y H:i' : 'd/m/Y';
                $this->attributes['date'] = Carbon::createFromFormat($format, $value);
            } else {
                $this->attributes['date'] = Carbon::parse($value);
            }
        } catch (\Throwable $e) {
            $this->attributes['date'] = Carbon::parse($value);
        }
    }

    public function getDateFormattedAttribute(): string
    {
        if (empty($this->attributes['date'])) {
            return '';
        }
        return Carbon::parse($this->attributes['date'])->format('d/m/Y H:i');
    }

    public function getFormattedCodeAttribute(): string
    {
        $code = $this->attributes['code'] ?? $this->attributes['id'] ?? 0;
        $length = (int) env("LENGTH_OF_TRANSACTION_ID", 6);
        return str_pad((string) $code, $length, "0", STR_PAD_LEFT);
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format((float) ($this->attributes['amount'] ?? 0), 2, '.', '');
    }

    public function getStatusLabelAttribute(): string
    {
        $status = $this->attributes['status'] ?? self::DRAFT;
        return self::statusConf()[$status][1] ?? 'Draft';
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'verify_by');
    }
}
