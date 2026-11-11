<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\PaymentMethod;
use App\Repositories\Base\BaseRepository;
use App\Repositories\PaymentMethodRepositoryInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(PaymentMethod::class)
             ->setRepositoryId('rinvex.repository.uniqueid');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
            $term = strtolower(trim((string)$value));
            $query->where(function ($q) use ($term) {
                $q->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%")
                  ->orWhere(DB::raw('LOWER(description)'), 'LIKE', "%{$term}%")
                  ->orWhere(DB::raw('LOWER(provider)'), 'LIKE', "%{$term}%");
            });
        });

        $paymentMethods = QueryBuilder::for(PaymentMethod::class)
            ->defaultSort('order', 'id')
            ->allowedFields([
                'id',
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
                'created_at',
                'updated_at',
            ])
            ->allowedSorts([
                'id',
                'name',
                'order',
                'print',
                'is_sponsor',
                'is_disable',
                'is_activate',
                'is_default_payment',
                'created_at',
            ])
            ->allowedFilters([
                'name',
                'provider',
                AllowedFilter::exact('print'),
                AllowedFilter::exact('is_sponsor'),
                AllowedFilter::exact('is_disable'),
                AllowedFilter::exact('is_activate'),
                AllowedFilter::exact('is_default_payment'),
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        return $paymentMethods;
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();

        // Action (on the left)
        $table->column(key: 'actions', label: 'Action');
        // 1. Name
        $table->column(key: 'name', label: 'Name', sortable: true, searchable: true);
        // 2. Default Print
        $table->column(key: 'print', label: 'Default Print', sortable: true);
        // 3. Default Payment
        $table->column(key: 'is_default_payment', label: 'Default Payment', sortable: true);
        // 4. Sponsored
        $table->column(key: 'is_sponsor', label: 'Sponsored', sortable: true);
        // 5. Online Payment
        $table->column(key: 'is_activate', label: 'Online Payment', sortable: true);
        // 6. Active
        $table->column(key: 'is_disable', label: 'Active', sortable: true);
        // 7. Description
        $table->column(key: 'description', label: 'Description');
        // 8. Order
        $table->column(key: 'order', label: 'Order', sortable: true);
    }

    public function createPaymentMethod(array $data, ?UploadedFile $qrCode = null): PaymentMethod
    {
        return DB::transaction(function () use ($data, $qrCode) {
            $data['user_id'] = auth()->id();

            // Single default rules: unset other defaults if this one is set
            if (!empty($data['is_default_payment'])) {
                PaymentMethod::query()->update(['is_default_payment' => false]);
            }
            if (!empty($data['print'])) {
                PaymentMethod::query()->update(['print' => false]);
            }

            // Upload QR Code if provided
            if ($qrCode && $qrCode->isValid()) {
                $ext = $qrCode->getClientOriginalExtension() ?: 'png';
                $filename = 'pm_qr_' . Str::random(20) . '.' . $ext;
                $data['qr_code'] = $qrCode->storeAs('payment_methods', $filename, 'public');
            }

            if (!isset($data['order']) || $data['order'] === '' || $data['order'] === null) {
                $data['order'] = $this->getNextOrder();
            }

            return PaymentMethod::create($data);
        });
    }

    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data, ?UploadedFile $qrCode = null, bool $removeQrCode = false): PaymentMethod
    {
        return DB::transaction(function () use ($paymentMethod, $data, $qrCode, $removeQrCode) {
            // Single default rules: unset other defaults if this one is set
            if (!empty($data['is_default_payment'])) {
                PaymentMethod::where('id', '!=', $paymentMethod->id)->update(['is_default_payment' => false]);
            }
            if (!empty($data['print'])) {
                PaymentMethod::where('id', '!=', $paymentMethod->id)->update(['print' => false]);
            }

            // Handle QR Code replacement or removal
            if ($removeQrCode) {
                if ($paymentMethod->qr_code && Storage::disk('public')->exists($paymentMethod->qr_code)) {
                    Storage::disk('public')->delete($paymentMethod->qr_code);
                }
                $data['qr_code'] = null;
            } elseif ($qrCode && $qrCode->isValid()) {
                if ($paymentMethod->qr_code && Storage::disk('public')->exists($paymentMethod->qr_code)) {
                    Storage::disk('public')->delete($paymentMethod->qr_code);
                }
                $ext = $qrCode->getClientOriginalExtension() ?: 'png';
                $filename = 'pm_qr_' . Str::random(20) . '.' . $ext;
                $data['qr_code'] = $qrCode->storeAs('payment_methods', $filename, 'public');
            } else {
                unset($data['qr_code']);
            }

            // Handle API key preservation in datas if masked
            if (isset($data['datas']) && is_array($data['datas'])) {
                $existingDatas = $paymentMethod->datas;
                if (is_string($existingDatas)) {
                    $existingDatas = json_decode($existingDatas, true) ?: [];
                }
                $incomingKey = $data['datas']['api_key'] ?? '';
                if (($incomingKey === '••••••••' || empty($incomingKey)) && !empty($existingDatas['api_key'])) {
                    $data['datas']['api_key'] = $existingDatas['api_key'];
                }
            }

            $paymentMethod->update($data);

            return $paymentMethod->fresh();
        });
    }

    public function deletePaymentMethod(PaymentMethod $paymentMethod): bool
    {
        return DB::transaction(function () use ($paymentMethod) {
            if ($paymentMethod->qr_code && Storage::disk('public')->exists($paymentMethod->qr_code)) {
                Storage::disk('public')->delete($paymentMethod->qr_code);
            }

            return (bool) $paymentMethod->delete();
        });
    }

    public function getNextOrder(): int
    {
        $max = PaymentMethod::max('order');
        return is_numeric($max) ? ((int)$max + 1) : 1;
    }
}
