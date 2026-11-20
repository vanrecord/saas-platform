<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\QuoteRequest;
use App\Models\Tenant\DentalCondition;
use App\Models\Tenant\Doctor;
use App\Models\Tenant\Estimate;
use App\Models\Tenant\EstimateDetail;
use App\Models\Tenant\Patient;
use App\Models\Tenant\Product;
use App\Models\Tenant\Service;
use App\Models\Tenant\Setting;
use App\Models\UserTenant;
use App\Repositories\EstimateRepositoryInterface;
use App\Supports\Trait\ClinicInfoTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class QuoteController extends Controller
{
    use ClinicInfoTrait;

    protected EstimateRepositoryInterface $estimateRepository;

    public function __construct(EstimateRepositoryInterface $estimateRepository)
    {
        $this->estimateRepository = $estimateRepository;
    }

    public function index(Request $request): Response
    {
        $this->authorizeAccess(['quotes:quotes:view', 'quote:view']);

        $quotes = $this->estimateRepository->getDataTable();

        $patients = Patient::query()
            ->select('id', 'id_number', 'firstname', 'lastname', 'phone')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'id_number' => $p->id_number ?: $p->id,
                    'full_name' => trim($p->firstname . ' ' . $p->lastname),
                    'phone' => $p->phone,
                ];
            });

        return Inertia::render('Tenant/Quote/Index', [
            'quotes' => $quotes,
            'patients' => $patients,
            'statusOptions' => Estimate::statusOptions(),
        ])->table(function (InertiaTable $table) {
            $this->estimateRepository->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        $this->authorizeAccess(['quotes:quotes:create', 'quote:create']);

        $patients = Patient::query()
            ->select('id', 'firstname', 'lastname', 'id_number', 'phone', 'gender', 'age', 'dob')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'full_name' => $p->full_name,
                    'id_number' => $p->id_number ?: $p->id,
                    'gender' => $p->gender,
                    'age' => $p->age,
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
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                ];
            });

        $services = Service::query()
            ->select('id', 'name', 'price', 'cost')
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->select('id', 'name', 'price', 'cost')
            ->orderBy('name')
            ->get();

        $conditions = DentalCondition::query()
            ->select('id', 'name', 'code', 'default_color')
            ->orderBy('id')
            ->get();

        $exchangeRate = (float) Setting::get('exchange_rate', 4050);
        $nextCode = $this->estimateRepository->getNextCode();

        return Inertia::render('Tenant/Quote/Create', [
            'suggestedCode' => $nextCode,
            'exchangeRate' => $exchangeRate,
            'patients' => $patients,
            'doctors' => $doctors,
            'assistants' => $assistants,
            'services' => $services,
            'products' => $products,
            'conditions' => $conditions,
        ]);
    }

    public function store(QuoteRequest $request): RedirectResponse
    {
        $this->authorizeAccess(['quotes:quotes:create', 'quotes:quotes:save', 'quote:create', 'quote:save']);

        $estimate = $this->estimateRepository->createEstimate($request->validated());

        return redirect()->route('quotes.index')->with('success', 'Quote created successfully.');
    }

    public function edit(int $id): Response
    {
        $this->authorizeAccess(['quotes:quotes:edit', 'quote:edit']);

        $quote = $this->estimateRepository->findWithDetails($id);

        $patients = Patient::query()
            ->select('id', 'firstname', 'lastname', 'id_number', 'phone', 'gender', 'age', 'dob')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'full_name' => $p->full_name,
                    'id_number' => $p->id_number ?: $p->id,
                    'gender' => $p->gender,
                    'age' => $p->age,
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
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                ];
            });

        $services = Service::query()
            ->select('id', 'name', 'price', 'cost')
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->select('id', 'name', 'price', 'cost')
            ->orderBy('name')
            ->get();

        $conditions = DentalCondition::query()
            ->select('id', 'name', 'code', 'default_color')
            ->orderBy('id')
            ->get();

        $exchangeRate = (float) ($quote->rate ?: Setting::get('exchange_rate', 4050));

        return Inertia::render('Tenant/Quote/Edit', [
            'quote' => $quote,
            'patients' => $patients,
            'doctors' => $doctors,
            'assistants' => $assistants,
            'services' => $services,
            'products' => $products,
            'conditions' => $conditions,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    public function update(QuoteRequest $request, int $id): RedirectResponse
    {
        $this->authorizeAccess(['quotes:quotes:edit', 'quotes:quotes:save', 'quote:edit', 'quote:save']);

        $this->estimateRepository->updateEstimate($id, $request->validated());

        return redirect()->route('quotes.index')->with('success', 'Quote updated successfully.');
    }

    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        $this->authorizeAccess(['quotes:quotes:delete', 'quote:delete']);

        try {
            $this->estimateRepository->deleteEstimate($id);

            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Quote deleted successfully.']);
            }

            return redirect()->route('quotes.index')->with('success', 'Quote deleted successfully.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->route('quotes.index')->with('error', $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $this->authorizeAccess(['quotes:quotes:view', 'quotes:quotes:view_detail', 'quote:view', 'quote:view-detail']);

        $quote = Estimate::with([
            'patient',
            'doctor.user.profile',
            'assistant.profile',
            'estimateDetails.detailable',
            'estimateDetails.teeth',
            'estimateDetails.approvedByUser',
            'estimateDetails.rejectedByUser',
            'invoice',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'quote' => $quote,
            'items' => $quote->estimateDetails->map(function ($item) {
                return [
                    'id' => $item->id,
                    'estimate_id' => $item->estimate_id,
                    'date' => $item->date ? Carbon::parse($item->date)->format('Y-m-d') : '',
                    'item_name' => $item->item_name,
                    'item_type' => $item->item_type,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'discount_as' => $item->discount_as,
                    'discount_dollars' => $item->discount_as === '%' ? ($item->quantity * $item->unit_price * ($item->discount / 100)) : $item->discount,
                    'amount' => $item->amount,
                    'status' => $item->status,
                    'status_label' => $item->status_label,
                    'teeth' => $item->teeth,
                    'teeth_numbers' => $item->teeth_numbers,
                    'approved_at' => $item->approved_at ? Carbon::parse($item->approved_at)->format('Y-m-d H:i') : null,
                    'rejected_at' => $item->rejected_at ? Carbon::parse($item->rejected_at)->format('Y-m-d H:i') : null,
                    'rejected_note' => $item->rejected_note,
                ];
            }),
        ]);
    }

    public function approveItem(Request $request, int $id, int $itemId): JsonResponse
    {
        $this->authorizeAccess(['quotes:quotes:approve', 'quote:approve']);

        $result = $this->estimateRepository->approveItem($id, $itemId);

        return response()->json($result);
    }

    public function rejectItem(Request $request, int $id, int $itemId): JsonResponse
    {
        $this->authorizeAccess(['quotes:quotes:reject', 'quote:reject']);

        $reason = $request->input('reason') ?: $request->input('rejected_note') ?: 'Rejected by user';
        $result = $this->estimateRepository->rejectItem($id, $itemId, $reason);

        return response()->json($result);
    }

    public function addItem(Request $request, int $id): JsonResponse
    {
        $this->authorizeAccess(['quotes:quotes:create', 'quotes:quotes:edit', 'quote:create', 'quote:edit']);

        $request->validate([
            'service_id' => ['nullable'],
            'detailable_id' => ['nullable'],
            'item_type' => ['nullable', 'string'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'discount_as' => ['nullable', 'string', 'in:%,$'],
            'date' => ['nullable'],
            'note' => ['nullable', 'string'],
            'teeth' => ['nullable'],
        ]);

        $result = $this->estimateRepository->addItem($id, $request->all());

        return response()->json($result);
    }

    public function print(int $id): Response
    {
        $this->authorizeAccess(['quotes:quotes:print', 'quotes:quotes:view', 'quote:print', 'quote:view']);

        $estimate = Estimate::with([
            'patient.addressRecord.province',
            'patient.addressRecord.district',
            'patient.addressRecord.commune',
            'patient.addressRecord.village',
            'doctor.user.profile',
            'assistant.profile',
            'estimateDetails.detailable',
            'estimateDetails.teeth',
        ])->findOrFail($id);

        $clinic = $this->getClinicInfo();
        $patient = $estimate->patient;

        $patientAddress = $patient?->formatted_address ?: ($patient?->address ?: '');
        $doctorName = $estimate->doctor?->full_name ?: ($estimate->doctor?->user?->name ?? '');
        $doctorPhone = $estimate->doctor?->user?->phone 
            ?? $estimate->doctor?->user?->profile?->phone 
            ?? '';

        $patientName = $patient?->full_name ?: 'N/A';
        $patientSex = $patient?->gender ?: $patient?->sex ?: 'M';
        $patientAge = $patient?->age ?: ($patient?->dob ? Carbon::parse($patient->dob)->age : '');
        $patientId = $patient ? str_pad((string) ($patient->id_number ?: $patient->id), 6, '0', STR_PAD_LEFT) : '';
        $patientPhone = $patient?->phone ?: '';
        $patientProfession = $patient?->profession ?: $patient?->occupation ?: '';

        $items = [];
        $subtotal = 0.0;
        foreach ($estimate->estimateDetails as $idx => $detail) {
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

        $overallDiscount = (float) ($estimate->discount_invoice ?: 0.0);
        $overallDiscountType = $estimate->discount_inv_as ?: '%';

        if ($overallDiscountType === '%') {
            $overallDiscountAmount = $subtotal * ($overallDiscount / 100);
        } else {
            $overallDiscountAmount = $overallDiscount;
        }
        $overallDiscountAmount = min($subtotal, max(0, $overallDiscountAmount));
        $total = max(0, $subtotal - $overallDiscountAmount);

        return Inertia::render('Tenant/Quote/Print', [
            'quote' => [
                'id' => $estimate->id,
                'number' => $estimate->quote_no,
                'code' => $estimate->code ?: $estimate->id,
                'date' => $estimate->estimate_at ? Carbon::parse($estimate->estimate_at)->format('d/m/Y') : Carbon::today()->format('d/m/Y'),
                'printed_at' => Carbon::now()->format('d/m/Y g:i A'),
                'subtotal' => $subtotal,
                'discount' => $overallDiscountAmount,
                'total' => $total,
                'rate' => (float) ($estimate->rate ?: 4050.0),
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
                    'profession' => $patientProfession,
                    'address' => $patientAddress,
                ],
                'cashier_name' => $estimate->assistant?->name ?? auth()->user()?->name ?? 'fwta owner',
            ],
            'clinic' => $clinic,
        ]);
    }

    protected function authorizeAccess(array $permissions): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        if ($user->can('all')) {
            return;
        }

        foreach ($permissions as $perm) {
            if ($user->can($perm)) {
                return;
            }
        }

        abort(403, 'Unauthorized action on Quote.');
    }
}
