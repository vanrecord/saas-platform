<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\PaymentMethodRequest;
use App\Models\Tenant\PaymentMethod;
use App\Repositories\PaymentMethodRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class PaymentMethodController extends Controller
{
    protected PaymentMethodRepositoryInterface $paymentMethodRepository;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function index(Request $request): Response
    {
        abort_if(!auth()->user()?->can('payment-method:view'), 403, 'Unauthorized to view payment methods.');

        $paymentMethods = $this->paymentMethodRepository->getDataTable();

        return Inertia::render('Tenant/Setting/PaymentMethod/Index', [
            'paymentMethods' => $paymentMethods,
        ])->table(function (InertiaTable $table) {
            $this->paymentMethodRepository->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        abort_if(!auth()->user()?->can('payment-method:create'), 403, 'Unauthorized to create payment methods.');

        $nextOrder = $this->paymentMethodRepository->getNextOrder();

        return Inertia::render('Tenant/Setting/PaymentMethod/Create', [
            'nextOrder' => $nextOrder,
        ]);
    }

    public function store(PaymentMethodRequest $request): RedirectResponse
    {
        abort_if(
            !auth()->user()?->can('payment-method:create') && !auth()->user()?->can('payment-method:save'),
            403,
            'Unauthorized to save payment methods.'
        );

        $paymentInfor = $this->paymentMethodRepository->createPaymentMethod(
            $request->validated(),
            $request->file('qr_code')
        );

        $fileQr = !empty($request->qr_code) ? $request->qr_code : "";
        $qrcode = $fileQr;

        if (!empty($qrcode) && $qrcode != null && $qrcode instanceof \Illuminate\Http\UploadedFile && $qrcode->isValid()) {
            if (!$paymentInfor->hasMedia('qrcode')) {
                $file_name     = $qrcode->getClientOriginalName();
                $extension     = explode(".", $file_name);
                $extension     = strtolower(end($extension));
                $new_file_name = md5(date("YmdHis") . $file_name);
                $new_name      = $new_file_name . '.' . $extension;

                $paymentInfor->addMedia($qrcode)->usingFileName($new_name)->toMediaCollection('qrcode', 'public');
            }
        }

        return redirect()->route('settings.payment-methods.index')
            ->with('flash.banner', 'Payment Method created successfully')
            ->with('flash.bannerStyle', 'success');
    }

    public function edit(PaymentMethod $paymentMethod): Response
    {
        abort_if(!auth()->user()?->can('payment-method:edit'), 403, 'Unauthorized to edit payment methods.');

        return Inertia::render('Tenant/Setting/PaymentMethod/Edit', [
            'paymentMethod' => [
                'id' => $paymentMethod->id,
                'name' => $paymentMethod->name,
                'description' => $paymentMethod->description,
                'order' => $paymentMethod->order,
                'print' => (bool) $paymentMethod->print,
                'is_disable' => (bool) $paymentMethod->is_disable,
                'is_sponsor' => (bool) $paymentMethod->is_sponsor,
                'is_default_payment' => (bool) $paymentMethod->is_default_payment,
                'provider' => $paymentMethod->provider ?: '',
                'is_activate' => (bool) $paymentMethod->is_activate,
                'currency' => $paymentMethod->currency ?: 'USD',
                'qr_code_url' => $paymentMethod->qr_code_url,
                'datas' => $paymentMethod->safe_datas,
            ],
        ]);
    }

    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        abort_if(
            !auth()->user()?->can('payment-method:edit') && !auth()->user()?->can('payment-method:save'),
            403,
            'Unauthorized to update payment methods.'
        );

        $paymentInfor = $this->paymentMethodRepository->updatePaymentMethod(
            $paymentMethod,
            $request->validated(),
            $request->file('qr_code'),
            $request->boolean('remove_qr_code')
        );

        $fileQr = !empty($request->qr_code) ? $request->qr_code : "";
        $qrcode = $fileQr;

        if ($request->boolean('remove_qr_code')) {
            $paymentInfor->clearMediaCollection('qrcode');
        } elseif (!empty($qrcode) && $qrcode != null && $qrcode instanceof \Illuminate\Http\UploadedFile && $qrcode->isValid()) {
            if (!$paymentInfor->hasMedia('qrcode')) {
                $file_name     = $qrcode->getClientOriginalName();
                $extension     = explode(".", $file_name);
                $extension     = strtolower(end($extension));
                $new_file_name = md5(date("YmdHis") . $file_name);
                $new_name      = $new_file_name . '.' . $extension;

                $paymentInfor->addMedia($qrcode)->usingFileName($new_name)->toMediaCollection('qrcode', 'public');
            }
        }

        return redirect()->route('settings.payment-methods.index')
            ->with('flash.banner', 'Payment Method updated successfully')
            ->with('flash.bannerStyle', 'success');
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        abort_if(!auth()->user()?->can('payment-method:delete'), 403, 'Unauthorized to delete payment methods.');

        if ($paymentMethod->payments()->exists()) {
            return redirect()->back()->withErrors([
                'error' => 'This Payment Method cannot be deleted because it is being used',
            ]);
        }

        $this->paymentMethodRepository->deletePaymentMethod($paymentMethod);

        return redirect()->route('settings.payment-methods.index')
            ->with('flash.banner', 'Payment Method deleted successfully')
            ->with('flash.bannerStyle', 'success');
    }
}
