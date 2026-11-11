<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\InvoiceRequest;
use App\Models\Tenant\DentalCondition;
use App\Models\Tenant\Doctor;
use App\Models\Tenant\MedicalHistory;
use App\Models\Tenant\Patient;
use App\Models\Tenant\Payment;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Prefix;
use App\Models\Tenant\Product;
use App\Models\Tenant\Service;
use App\Models\UserTenant;
use App\Repositories\InvoicePrefixRepositoryInterface;
use App\Repositories\InvoiceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class InvoiceController extends Controller
{
    protected InvoiceRepositoryInterface $invoiceRepository;
    protected InvoicePrefixRepositoryInterface $prefixRepository;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        InvoicePrefixRepositoryInterface $prefixRepository
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->prefixRepository = $prefixRepository;
    }

    public function index(): Response
    {
        abort_if(!auth()->user()?->can('invoice:view'), 403, 'Unauthorized to view invoices.');

        $invoices = $this->invoiceRepository->getDataTable();

        // Patients for filter dropdown
        $patients = Patient::select('id', 'id_number', 'firstname', 'lastname', 'phone')
            ->orderBy('firstname')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'id_number' => $p->id_number,
                    'full_name' => trim($p->firstname . ' ' . $p->lastname),
                    'phone' => $p->phone,
                ];
            });

        return Inertia::render('Tenant/Invoice/Index', [
            'invoices' => $invoices,
            'patients' => $patients,
        ])->table(function (InertiaTable $table) {
            $this->invoiceRepository->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        abort_if(!auth()->user()?->can('invoice:create'), 403, 'Unauthorized to create invoices.');

        $defaultPrefix = $this->prefixRepository->getDefault();
        $nextInvoiceInfo = $this->invoiceRepository->generateNextInvoiceNumber(
            $defaultPrefix?->id,
            Carbon::today()->format('Y-m-d')
        );

        $exchangeRateSetting = DB::table('settings')
            ->where('setting_key', 'exchange_rate')
            ->value('setting_value');
        $exchangeRate = $exchangeRateSetting ? (float) $exchangeRateSetting : 4050.0;

        $patients = Patient::select('id', 'id_number', 'firstname', 'lastname', 'phone', 'gender', 'dob')
            ->orderBy('firstname')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'id_number' => $p->id_number,
                    'firstname' => $p->firstname,
                    'lastname' => $p->lastname,
                    'full_name' => trim($p->firstname . ' ' . $p->lastname),
                    'phone' => $p->phone,
                ];
            });

        $doctors = Doctor::with('user.profile')->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'name' => $d->full_name ?: ($d->user?->name ?? 'Doctor #' . $d->id),
            ];
        });

        $assistants = UserTenant::with('profile')->get()->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->profile?->full_name ?: ($u->name ?? 'User #' . $u->id),
            ];
        });

        $services = Service::where('status', true)
            ->select('id', 'name', 'price', 'cost', 'code')
            ->orderBy('name')
            ->get();

        $products = Product::where('status', true)
            ->select('id', 'name', 'price', 'cost', 'code', 'qty')
            ->orderBy('name')
            ->get();

        $conditions = DentalCondition::where('is_active', true)
            ->select('id', 'name', 'code', 'default_color')
            ->orderBy('id')
            ->get();

        return Inertia::render('Tenant/Invoice/Create', [
            'suggestedInvoiceNumber' => $nextInvoiceInfo['invoice_number'],
            'defaultPrefix' => $defaultPrefix,
            'exchangeRate' => $exchangeRate,
            'patients' => $patients,
            'doctors' => $doctors,
            'assistants' => $assistants,
            'services' => $services,
            'products' => $products,
            'conditions' => $conditions,
        ]);
    }

    public function store(InvoiceRequest $request): RedirectResponse
    {
        abort_if(!auth()->user()?->can('invoice:save') && !auth()->user()?->can('invoice:create'), 403, 'Unauthorized to save invoices.');

        $this->invoiceRepository->createInvoice($request->validated());

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function edit($id): Response
    {
        abort_if(!auth()->user()?->can('invoice:edit'), 403, 'Unauthorized to edit invoices.');

        $invoice = $this->invoiceRepository->findWithDetails($id);

        $patients = Patient::select('id', 'id_number', 'firstname', 'lastname', 'phone', 'gender', 'dob')
            ->orderBy('firstname')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'id_number' => $p->id_number,
                    'firstname' => $p->firstname,
                    'lastname' => $p->lastname,
                    'full_name' => trim($p->firstname . ' ' . $p->lastname),
                    'phone' => $p->phone,
                ];
            });

        $doctors = Doctor::with('user.profile')->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'name' => $d->full_name ?: ($d->user?->name ?? 'Doctor #' . $d->id),
            ];
        });

        $assistants = UserTenant::with('profile')->get()->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->profile?->full_name ?: ($u->name ?? 'User #' . $u->id),
            ];
        });

        $services = Service::where('status', true)
            ->select('id', 'name', 'price', 'cost', 'code')
            ->orderBy('name')
            ->get();

        $products = Product::where('status', true)
            ->select('id', 'name', 'price', 'cost', 'code', 'qty')
            ->orderBy('name')
            ->get();

        $conditions = DentalCondition::where('is_active', true)
            ->select('id', 'name', 'code', 'default_color')
            ->orderBy('id')
            ->get();

        return Inertia::render('Tenant/Invoice/Edit', [
            'invoice' => $invoice,
            'patients' => $patients,
            'doctors' => $doctors,
            'assistants' => $assistants,
            'services' => $services,
            'products' => $products,
            'conditions' => $conditions,
        ]);
    }

    public function update(InvoiceRequest $request, $id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('invoice:save') && !auth()->user()?->can('invoice:edit'), 403, 'Unauthorized to update invoices.');

        $this->invoiceRepository->updateInvoice($id, $request->validated());

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    public function show($id): Response
    {
        abort_if(!auth()->user()?->can('invoice:view'), 403, 'Unauthorized to view invoices.');

        $invoice = $this->invoiceRepository->findWithDetails($id);

        return Inertia::render('Tenant/Invoice/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('invoice:delete'), 403, 'Unauthorized to delete invoices.');

        $this->invoiceRepository->deleteInvoice($id);

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function paymentHistory($id): JsonResponse
    {
        abort_if(!auth()->user()?->can('invoice:view'), 403, 'Unauthorized to view invoice payment history.');

        $invoice = MedicalHistory::with([
            'patient',
            'payments.paymentMethod',
            'payments.verifiedBy.profile',
        ])->findOrFail($id);

        $patient = $invoice->patient;

        // Active payment methods
        $paymentMethods = PaymentMethod::where(function ($q) {
            $q->whereNull('is_activate')->orWhere('is_activate', true);
        })->where(function ($q) {
            $q->whereNull('is_disable')->orWhere('is_disable', false);
        })
        ->orderBy('order')
        ->orderBy('name')
        ->get();

        // Build account options (e.g. from payment methods register_ref or default)
        $accounts = [];
        foreach ($paymentMethods as $pm) {
            if (!empty($pm->register_ref)) {
                $curr = $pm->currency ?: 'USD';
                $acc = $pm->register_ref . '_' . $curr;
                if (!in_array($acc, $accounts)) {
                    $accounts[] = $acc;
                }
            }
        }
        // Always ensure standard accounts exist with 00047_USD as first if not present
        if (!in_array('00047_USD', $accounts)) {
            array_unshift($accounts, '00047_USD');
        }
        if (!in_array('00047_KHR', $accounts)) {
            $accounts[] = '00047_KHR';
        }
        if (!in_array('CASH_USD', $accounts)) {
            $accounts[] = 'CASH_USD';
        }

        // Transactions list
        $transactions = $invoice->payments()
            ->with(['paymentMethod', 'verifiedBy.profile'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($p) use ($invoice) {
                return [
                    'id' => $p->id,
                    'date' => $p->date ? Carbon::parse($p->date)->format('Y-m-d H:i:s') : null,
                    'patient_name' => $invoice->patient ? trim($invoice->patient->firstname . ' ' . $invoice->patient->lastname) : '-',
                    'invoice_number' => $invoice->invoice_number_formatted,
                    'currency' => $p->currency ?: 'USD',
                    'amount' => (float) $p->amount,
                    'amount_kh' => (int) $p->amount_kh,
                    'payment_method_id' => $p->payment_method_id,
                    'payment_method_name' => $p->paymentMethod?->name ?: ($p->amount < 0 ? 'Refund' : 'Cash'),
                    'txnref' => $p->txnref,
                    'status' => $p->status ?: 'completed',
                ];
            });

        $totalPaidUsd = (float) $invoice->payments()->where('amount', '>', 0)->sum('amount');
        $totalPaidKh = (int) $invoice->payments()->where('amount_kh', '>', 0)->sum('amount_kh');
        $refundedUsd = (float) $invoice->payments()->where('amount', '<', 0)->sum(function ($p) {
            return abs((float) $p->amount);
        });
        $outstandingBalance = max(0, (float) $invoice->total - $totalPaidUsd + $refundedUsd);
        $availableCredit = $patient ? (float) ($patient->balance ?? 0) : 0.0;

        return response()->json([
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_number_formatted' => $invoice->invoice_number_formatted,
                'date' => $invoice->date ? Carbon::parse($invoice->date)->format('Y-m-d') : null,
                'total' => (float) $invoice->total,
                'paid_usd' => (float) $invoice->paid_usd,
                'paid_kh' => (int) $invoice->paid_kh,
                'balance' => (float) $invoice->balance,
                'rate' => (float) ($invoice->rate ?: 4050.0),
                'status' => $invoice->status,
                'status_label' => $invoice->status_label,
            ],
            'patient' => [
                'id' => $patient?->id,
                'name' => $patient ? trim($patient->firstname . ' ' . $patient->lastname) : '',
                'phone' => $patient?->phone,
                'available_credit' => $availableCredit,
            ],
            'payment_methods' => $paymentMethods->map(function ($pm) {
                return [
                    'id' => $pm->id,
                    'name' => $pm->name,
                    'provider' => $pm->provider,
                    'is_sponsor' => (bool) $pm->is_sponsor,
                    'order' => $pm->order,
                    'qr_code_url' => $pm->qr_code_url,
                    'register_ref' => $pm->register_ref,
                    'currency' => $pm->currency,
                    'safe_datas' => $pm->safe_datas,
                ];
            }),
            'payment_accounts' => $accounts,
            'transactions' => $transactions,
            'summary' => [
                'total_paid_usd' => $totalPaidUsd,
                'total_paid_kh' => $totalPaidKh,
                'refunded_usd' => $refundedUsd,
                'outstanding_balance' => $outstandingBalance,
                'available_credit' => $availableCredit,
            ],
        ]);
    }

    public function storePayment(Request $request, $id): JsonResponse
    {
        abort_if(!auth()->user()?->can('invoice:save') && !auth()->user()?->can('invoice:edit') && !auth()->user()?->can('payment:create'), 403, 'Unauthorized to add payments.');

        $request->validate([
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required',
            'payment_account' => 'nullable|string|max:100',
            'apply_credit' => 'nullable|boolean',
            'credit_amount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $invoice = MedicalHistory::with('patient')->lockForUpdate()->findOrFail($id);
            $patient = $invoice->patient;

            // Current state
            $existingPayments = Payment::where('medical_history_id', $invoice->id)->get();
            $currentPaidUsd = (float) $existingPayments->where('amount', '>', 0)->sum('amount');
            $currentRefundedUsd = (float) $existingPayments->where('amount', '<', 0)->sum(fn($p) => abs($p->amount));
            $currentBalance = max(0, (float) $invoice->total - $currentPaidUsd + $currentRefundedUsd);

            $rate = (float) ($invoice->rate ?: 4050.0);
            $paymentDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();
            $paymentAccount = $request->input('payment_account') ?: '00047_USD';

            // 1. Process Applied Credit if requested
            $appliedCredit = 0.0;
            if ($request->boolean('apply_credit') && $patient) {
                $patientBalance = max(0, (float) ($patient->balance ?? 0));
                if ($patientBalance > 0 && $currentBalance > 0) {
                    $requestedCredit = $request->input('credit_amount') !== null ? (float) $request->input('credit_amount') : $currentBalance;
                    $appliedCredit = min($requestedCredit, $patientBalance, $currentBalance);

                    if ($appliedCredit > 0) {
                        // Deduct from patient's account credit
                        $patient->balance = max(0, (float) $patient->balance - $appliedCredit);
                        $patient->save();

                        // Record credit payment
                        Payment::create([
                            'patient_id' => $invoice->patient_id,
                            'medical_history_id' => $invoice->id,
                            'date' => $paymentDate,
                            'amount' => $appliedCredit,
                            'amount_kh' => (int) round($appliedCredit * $rate),
                            'payment_method_id' => $request->input('payment_method_id'),
                            'user_id' => auth()->id(),
                            'credit_balance' => $patient->balance,
                            'txnref' => 'CREDIT_APPLIED',
                            'currency' => 'USD',
                            'status' => 'completed',
                        ]);

                        $currentBalance = max(0, $currentBalance - $appliedCredit);
                    }
                }
            }

            // 2. Process Cash / Bank / Method Payment
            $amount = (float) $request->input('amount', 0);
            if ($amount > 0) {
                // Prevent overpayment
                if ($amount > ($currentBalance + 0.001)) {
                    throw ValidationException::withMessages([
                        'amount' => ["Payment amount ($" . number_format($amount, 2) . ") cannot exceed outstanding balance ($" . number_format($currentBalance, 2) . ")."],
                    ]);
                }

                $amountKh = (int) round($amount * $rate);

                Payment::create([
                    'patient_id' => $invoice->patient_id,
                    'medical_history_id' => $invoice->id,
                    'date' => $paymentDate,
                    'amount' => $amount,
                    'amount_kh' => $amountKh,
                    'payment_method_id' => $request->input('payment_method_id'),
                    'user_id' => auth()->id(),
                    'txnref' => $paymentAccount,
                    'currency' => 'USD',
                    'status' => 'completed',
                ]);
            }

            // 3. Recalculate invoice balances and status
            $allPayments = Payment::where('medical_history_id', $invoice->id)->get();
            $paidUsd = (float) $allPayments->where('amount', '>', 0)->sum('amount');
            $paidKh = (float) $allPayments->where('amount_kh', '>', 0)->sum('amount_kh');
            $refunded = (float) $allPayments->where('amount', '<', 0)->sum(fn($p) => abs($p->amount));
            $newBalance = max(0, (float) $invoice->total - $paidUsd + $refunded);

            $status = MedicalHistory::determineStatus((float) $invoice->total, $paidUsd, $refunded, $newBalance);

            $invoice->update([
                'paid_usd' => $paidUsd,
                'paid_kh' => $paidKh,
                'refunded' => $refunded,
                'balance' => $newBalance,
                'status' => $status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'invoice' => [
                    'id' => $invoice->id,
                    'paid_usd' => $paidUsd,
                    'paid_kh' => $paidKh,
                    'balance' => $newBalance,
                    'status' => $status,
                    'status_label' => $invoice->fresh()->status_label,
                ],
            ]);
        });
    }

    public function statement($id): Response
    {
        abort_if(!auth()->user()?->can('invoice:view'), 403, 'Unauthorized.');

        $invoice = MedicalHistory::with([
            'patient',
            'doctor.user.profile',
            'payments.paymentMethod',
            'payments.verifiedBy.profile',
        ])->findOrFail($id);

        $hospital = null;
        if (function_exists('tenant') && tenant()) {
            $hospital = DB::table('hospitals')->first();
        }

        return Inertia::render('Tenant/Invoice/Statement', [
            'invoice' => $invoice,
            'hospital' => $hospital,
        ]);
    }
}
