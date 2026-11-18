<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\InvoiceRequest;
use App\Models\Tenant\DentalCondition;
use App\Models\Tenant\Doctor;
use App\Models\Tenant\MedicalHistory;
use App\Models\Tenant\Medicine;
use App\Models\Tenant\Patient;
use App\Models\Tenant\Payment;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Prefix;
use App\Models\Tenant\Product;
use App\Models\Tenant\Service;
use App\Models\UserTenant;
use App\Repositories\InvoicePrefixRepositoryInterface;
use App\Repositories\InvoiceRepositoryInterface;
use App\Supports\Trait\ClinicInfoTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class InvoiceController extends Controller
{
    use ClinicInfoTrait;

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
        $assistants = UserTenant::query()
            ->with(['profile'])
            ->where(function ($query) {
                $query->where('is_super_admin', false)
                    ->orWhereNull('is_super_admin');
            })
            ->where(function ($query) {
                $query->whereNotIn('level_access', [UserTenant::SUPER_ADMIN, UserTenant::ADMIN, UserTenant::USER_DEV])
                    ->orWhereNull('level_access');
            })
            ->doesntHave('doctor')
            ->whereDoesntHave('roles', function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['doctor']);
            })
            ->get()
            ->map(function ($u) {
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

        $assistants = UserTenant::query()
            ->with(['profile'])
            ->where(function ($query) {
                $query->where('is_super_admin', false)
                    ->orWhereNull('is_super_admin');
            })
            ->where(function ($query) {
                $query->whereNotIn('level_access', [UserTenant::SUPER_ADMIN, UserTenant::ADMIN, UserTenant::USER_DEV])
                    ->orWhereNull('level_access');
            })
            ->doesntHave('doctor')
            ->whereDoesntHave('roles', function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['doctor']);
            })
            ->get()
            ->map(function ($u) {
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

        $medicines = Medicine::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Tenant/Invoice/Edit', [
            'invoice' => $invoice,
            'patients' => $patients,
            'doctors' => $doctors,
            'assistants' => $assistants,
            'services' => $services,
            'products' => $products,
            'conditions' => $conditions,
            'medicines' => $medicines,
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
            'medicalDetails',
            'payments.paymentMethod',
            'payments.verifiedBy.profile',
        ])->findOrFail($id);

        $invoice->recalculateFinancials(false);

        $patient = $invoice->patient;

        // All active payment methods
        $allActiveMethods = PaymentMethod::where('is_disable', false)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        // ABA KHQR methods: must be active (is_disable = false) and have valid, non-empty integration datas
        $abaMethods = $allActiveMethods->filter(function ($pm) {
            return $pm->hasValidAbaData();
        })->values();

        // Cash methods: active methods without ABA integration requirements
        $cashMethods = $allActiveMethods->filter(function ($pm) {
            return !$pm->hasValidAbaData();
        })->values();

        // If no dedicated cash-only methods exist, allow active methods in cash selection
        if ($cashMethods->isEmpty() && $allActiveMethods->isNotEmpty()) {
            $cashMethods = $allActiveMethods;
        }

        // Grouping by currency preserving legacy structure
        $groupedAba = [];
        foreach ($abaMethods->groupBy('currency') as $curr => $items) {
            $key = $curr ?: 'USD';
            $groupedAba[$key] = $items->values()->map(function ($pm) {
                return [
                    'id' => $pm->id,
                    'name' => $pm->name,
                    'currency' => $pm->currency,
                    'provider' => $pm->provider,
                    'register_ref' => $pm->register_ref,
                    'is_default_payment' => (bool) $pm->is_default_payment,
                    'order' => $pm->order,
                ];
            });
        }

        $groupedCash = [];
        foreach ($cashMethods->groupBy('currency') as $curr => $items) {
            $key = $curr ?: 'CASH PAYMENT';
            $groupedCash[$key] = $items->values()->map(function ($pm) {
                return [
                    'id' => $pm->id,
                    'name' => $pm->name,
                    'currency' => $pm->currency,
                    'provider' => $pm->provider,
                    'register_ref' => $pm->register_ref,
                    'is_default_payment' => (bool) $pm->is_default_payment,
                    'order' => $pm->order,
                ];
            });
        }

        // Default Payment & Auto-Selection logic:
        // Only auto-select if a payment method explicitly has is_default_payment = true.
        $hasValidAba = $abaMethods->isNotEmpty();
        $defaultMethod = $allActiveMethods->firstWhere('is_default_payment', true);

        $activePayment = $hasValidAba ? 'khqr' : 'cash';
        $selectedDefaultMethod = null;

        if ($defaultMethod && !$defaultMethod->is_disable) {
            if ($defaultMethod->hasValidAbaData()) {
                $activePayment = 'khqr';
                $selectedDefaultMethod = $defaultMethod;
            } else {
                $activePayment = 'cash';
                $selectedDefaultMethod = $defaultMethod;
            }
        }

        $defaultCurrency = $selectedDefaultMethod?->currency ?: 'USD';

        // Build account options (from active payment methods register_ref)
        $accounts = [];
        foreach ($allActiveMethods as $pm) {
            if (!empty($pm->register_ref)) {
                $curr = $pm->currency ?: 'USD';
                $acc = $pm->register_ref . '_' . $curr;
                if (!in_array($acc, $accounts)) {
                    $accounts[] = $acc;
                }
            }
        }

        // Payments collection (already eager loaded)
        $payments = $invoice->payments;

        // Transactions list sorted by date/id descending
        $transactions = $payments
            ->sortByDesc('id')
            ->values()
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

        $totalPaidUsd = (float) $payments->where('amount', '>', 0)->sum('amount');
        $totalPaidKh = (int) $payments->where('amount_kh', '>', 0)->sum('amount_kh');
        $refundedUsd = (float) $payments->where('amount', '<', 0)->sum(function ($p) {
            return abs((float) $p->amount);
        });
        $outstandingBalance = max(0, (float) $invoice->balance);
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
            'active_payment' => $activePayment,
            'has_valid_aba' => $hasValidAba,
            'default_method_id' => $selectedDefaultMethod?->id,
            'default_currency' => $defaultCurrency,
            'aba_payment_methods' => $abaMethods->map(function ($pm) {
                return [
                    'id' => $pm->id,
                    'name' => $pm->name,
                    'provider' => $pm->provider,
                    'is_sponsor' => (bool) $pm->is_sponsor,
                    'is_default_payment' => (bool) $pm->is_default_payment,
                    'order' => $pm->order,
                    'qr_code_url' => $pm->qr_code_url,
                    'register_ref' => $pm->register_ref,
                    'currency' => $pm->currency,
                    'safe_datas' => $pm->safe_datas,
                ];
            }),
            'cash_payment_methods' => $cashMethods->map(function ($pm) {
                return [
                    'id' => $pm->id,
                    'name' => $pm->name,
                    'provider' => $pm->provider,
                    'is_sponsor' => (bool) $pm->is_sponsor,
                    'is_default_payment' => (bool) $pm->is_default_payment,
                    'order' => $pm->order,
                    'qr_code_url' => $pm->qr_code_url,
                    'register_ref' => $pm->register_ref,
                    'currency' => $pm->currency,
                    'safe_datas' => $pm->safe_datas,
                ];
            }),
            'grouped_aba_methods' => $groupedAba,
            'grouped_cash_methods' => $groupedCash,
            'payment_methods' => $allActiveMethods->map(function ($pm) {
                return [
                    'id' => $pm->id,
                    'name' => $pm->name,
                    'provider' => $pm->provider,
                    'is_sponsor' => (bool) $pm->is_sponsor,
                    'is_default_payment' => (bool) $pm->is_default_payment,
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
            'payment_type' => 'nullable|string|in:khqr,cash',
            'amount' => 'required|numeric|min:0',
            'date' => 'required',
            'payment_account' => 'nullable|string|max:100',
            'apply_credit' => 'nullable|boolean',
            'credit_amount' => 'nullable|numeric|min:0',
        ]);

        if ((float) $request->input('amount', 0) > 0 && empty($request->input('payment_method_id'))) {
            throw ValidationException::withMessages([
                'payment_method_id' => ['Please select a payment method.'],
            ]);
        }

        if ($methodId = $request->input('payment_method_id')) {
            $pm = PaymentMethod::find($methodId);
            if (!$pm || $pm->is_disable) {
                throw ValidationException::withMessages([
                    'payment_method_id' => ['The selected payment method is inactive or disabled.'],
                ]);
            }
            if ($request->input('payment_type') === 'khqr' && !$pm->hasValidAbaData()) {
                throw ValidationException::withMessages([
                    'payment_method_id' => ['The selected payment method does not have valid ABA integration data.'],
                ]);
            }
        }

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
            $paymentAccount = $request->input('payment_account');
            if ($paymentAccount === 'null' || empty($paymentAccount)) {
                $paymentAccount = null;
            }

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
                            'credit_balance' => $patient->balance,
                            'txnref' => 'CREDIT_APPLIED',
                            'currency' => 'USD',
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
                    'txnref' => $paymentAccount,
                    'currency' => 'USD',
                ]);
            }

            // 3. Recalculate invoice balances and status
            $financials = $invoice->recalculateFinancials(true);
            $paidUsd = $financials['paid_usd'];
            $paidKh = $financials['paid_kh'];
            $newBalance = $financials['balance'];

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'invoice' => [
                    'id' => $invoice->id,
                    'paid_usd' => $paidUsd,
                    'paid_kh' => $paidKh,
                    'balance' => $newBalance,
                    'status' => $financials['status'],
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

    /**
     * Dedicated Invoice Print View matching reference PDF.
     */
    public function print($id): Response
    {
        abort_if(!auth()->user()?->can('invoice:view') && !auth()->user()?->can('invoice:edit'), 403, 'Unauthorized.');

        $invoice = MedicalHistory::with([
            'patient.addressRecord.province',
            'patient.addressRecord.district',
            'patient.addressRecord.commune',
            'patient.addressRecord.village',
            'doctor.user.profile',
            'assistant.profile',
            'medicalDetails.detailable',
            'medicalDetails.treatmentTeeth',
            'payments.paymentMethod',
            'prefix',
        ])->findOrFail($id);

        $clinic = $this->getClinicInfo();
        $patient = $invoice->patient;

        // Address resolution: column first, then addresses table (as per requirement)
        $patientAddress = $patient?->formatted_address ?: ($patient?->address ?: '');

        // Doctor details
        $doctorName = $invoice->doctor?->full_name 
            ?: ($invoice->doctor?->user?->name ?? '');
        $doctorPhone = $invoice->doctor?->user?->phone 
            ?? $invoice->doctor?->user?->profile?->phone 
            ?? '';

        // Patient details
        $patientName = $patient?->full_name ?: 'N/A';
        $patientSex = $patient?->gender ?: $patient?->sex ?: 'M';
        $patientAge = $patient?->age ?: ($patient?->dob ? Carbon::parse($patient->dob)->age : '');
        $patientId = $patient ? str_pad((string) ($patient->id_number ?: $patient->id), 6, '0', STR_PAD_LEFT) : '';
        $patientPhone = $patient?->phone ?: '';

        // Line items
        $items = [];
        $subtotal = 0.0;
        foreach ($invoice->medicalDetails as $idx => $detail) {
            $qty = max(1, (int) ($detail->quantity ?: 1));
            $unitPrice = (float) ($detail->unit_price ?: 0.0);
            $gross = $qty * $unitPrice;
            $discVal = (float) ($detail->discount ?: 0.0);
            $discAs = $detail->discount_as ?: '%';

            if ($discAs === '%') {
                $discPercent = $discVal;
                $discAmount = $gross * ($discVal / 100);
            } else {
                $discAmount = min($gross, $discVal);
                $discPercent = $gross > 0 ? ($discAmount / $gross) * 100 : 0.0;
            }

            $amount = (float) ($detail->amount ?? ($gross - $discAmount));
            $subtotal += $amount;

            $items[] = [
                'n' => $idx + 1,
                'name' => $detail->item_name ?: ($detail->detailable?->name ?? 'Treatment #' . ($idx + 1)),
                'qty' => $qty,
                'price' => $unitPrice,
                'disc_percent' => $discPercent,
                'disc_amount' => $discAmount,
                'amount' => $amount,
            ];
        }

        $rate = (float) ($invoice->rate ?: 4100.0);
        $overallDiscount = (float) ($invoice->discount_invoice ?: 0.0);
        $overallDiscountType = $invoice->discount_inv_as ?: '%';

        if ($overallDiscountType === '%') {
            $overallDiscountAmount = $subtotal * ($overallDiscount / 100);
        } else {
            $overallDiscountAmount = $overallDiscount;
        }
        $overallDiscountAmount = min($subtotal, max(0, $overallDiscountAmount));
        $total = max(0, $subtotal - $overallDiscountAmount);

        // Payments and Balance
        $paidUsd = (float) ($invoice->paid_usd ?? 0.0);
        $paidKh = (float) ($invoice->paid_kh ?? ($paidUsd * $rate));
        $balanceUsd = (float) ($invoice->balance ?? max(0, $total - $paidUsd));
        $balanceKh = (float) round($balanceUsd * $rate);

        // Invoice Number format
        $invNum = $invoice->invoice_number_formatted ?: $invoice->invoice_number;

        // Quotation number
        $quoteNum = !empty($invoice->estimate_id) ? sprintf('QUT%06d', $invoice->estimate_id) : null;

        return Inertia::render('Tenant/Invoice/Print', [
            'invoice' => [
                'id' => $invoice->id,
                'number' => $invNum,
                'quotation_number' => $quoteNum,
                'date' => $invoice->date ? Carbon::parse($invoice->date)->format('d/m/Y') : Carbon::today()->format('d/m/Y'),
                'printed_at' => Carbon::now()->format('d/m/Y g:i A'),
                'subtotal' => $subtotal,
                'discount' => $overallDiscountAmount,
                'total' => $total,
                'rate' => $rate,
                'paid_usd' => $paidUsd,
                'paid_kh' => $paidKh,
                'balance_usd' => $balanceUsd,
                'balance_kh' => $balanceKh,
                'items' => $items,
                'doctor' => [
                    'name' => $doctorName,
                    'phone' => $doctorPhone,
                ],
                'patient' => [
                    'name' => $patientName,
                    'sex' => $patientSex,
                    'age' => $patientAge,
                    'id' => $patientId,
                    'phone' => $patientPhone,
                    'address' => $patientAddress,
                ],
                'cashier_name' => $invoice->assistant?->name ?? auth()->user()?->name ?? 'fwta owner',
            ],
            'clinic' => $clinic,
        ]);
    }
}
