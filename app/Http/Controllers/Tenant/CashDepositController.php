<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CashDepositRequest;
use App\Models\Tenant\FundTransfer;
use App\Models\Tenant\Patient;
use App\Repositories\FundTransferRepositoryInterface;
use App\Supports\Trait\ClinicInfoTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class CashDepositController extends Controller
{
    use ClinicInfoTrait;

    protected FundTransferRepositoryInterface $fundTransferRepository;

    public function __construct(FundTransferRepositoryInterface $fundTransferRepository)
    {
        $this->fundTransferRepository = $fundTransferRepository;
    }

    /**
     * Authorize user access against permissions.
     */
    protected function authorizePermission(array|string $permissions): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->can('all') || ($user->is_super_admin ?? false)) {
            return;
        }

        $perms = (array) $permissions;
        foreach ($perms as $perm) {
            if ($user->can($perm)) {
                return;
            }
        }

        abort(403, 'Unauthorized to perform this action.');
    }

    /**
     * Display grouped cash deposits list.
     */
    public function index(Request $request): Response|JsonResponse
    {
        $this->authorizePermission([
            'all',
            'accounting:cashDeposit:view',
            'cash-deposit:view',
        ]);

        $deposits = $this->fundTransferRepository->getDataTable();
        $nextCode = $this->fundTransferRepository->getIdNumber();

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json($deposits);
        }

        return Inertia::render('Tenant/CashDeposit/Index', [
            'deposits' => $deposits,
            'nextCode' => $nextCode,
        ])->table(function (InertiaTable $table) {
            $this->fundTransferRepository->columnsDataTable($table);
        });
    }

    /**
     * Store new cash deposit record.
     */
    public function store(CashDepositRequest $request): JsonResponse
    {
        $this->authorizePermission([
            'all',
            'accounting:cashDeposit:create',
            'cash-deposit:create',
            'cash-deposit:save',
        ]);

        $data = $request->validated();

        if (empty($data['code'])) {
            $data['code'] = $this->fundTransferRepository->getIdNumber();
        } else {
            $data['code'] = (int) $data['code'];
        }

        $data['is_cash'] = !empty($data['is_cash']);
        $data['base_amount'] = (float) $data['amount'];
        $data['currency'] = !empty($data['currency']) ? strtoupper($data['currency']) : 'USD';
        $data['status'] = FundTransfer::DRAFT;
        $data['is_verify'] = false;

        if (empty($data['date'])) {
            $data['date'] = Carbon::now();
        }

        $record = $this->fundTransferRepository->create($data);

        return response()->json([
            'title' => 'Successfully',
            'successful' => 'The record was successfully saved.',
            'data' => $record,
        ]);
    }

    /**
     * Show single cash deposit details for edit.
     */
    public function show($id): JsonResponse
    {
        $this->authorizePermission([
            'all',
            'accounting:cashDeposit:view',
            'cash-deposit:view',
        ]);

        $deposit = FundTransfer::with(['patient', 'creator', 'verifier'])->findOrFail($id);

        return response()->json([
            'data' => [
                'id' => (int) $deposit->id,
                'code' => $deposit->formatted_code,
                'code_raw' => (int) $deposit->code,
                'patient_id' => (int) $deposit->patient_id,
                'patient' => $deposit->patient ? [
                    'id' => (int) $deposit->patient->id,
                    'full_name' => $deposit->patient->full_name,
                    'id_number' => $deposit->patient->id_number,
                    'phone' => $deposit->patient->phone,
                    'balance' => (float) ($deposit->patient->balance ?? 0),
                ] : null,
                'is_cash' => (bool) $deposit->is_cash,
                'status' => (int) $deposit->status,
                'status_label' => $deposit->status_label,
                'currency' => $deposit->currency ?: 'USD',
                'amount' => (float) $deposit->amount,
                'base_amount' => (float) $deposit->base_amount,
                'date' => $deposit->date ? Carbon::parse($deposit->date)->format('Y-m-d') : '',
                'date_formatted' => $deposit->date_formatted,
                'is_verify' => (bool) $deposit->is_verify,
                'note' => $deposit->note ?? '',
            ],
        ]);
    }

    /**
     * Update an unverified deposit record.
     */
    public function update(CashDepositRequest $request, $id): JsonResponse
    {
        $this->authorizePermission([
            'all',
            'accounting:cashDeposit:edit',
            'cash-deposit:edit',
            'cash-deposit:save',
        ]);

        $deposit = FundTransfer::findOrFail($id);

        if ($deposit->is_verify || $deposit->status === FundTransfer::VERIFIED) {
            return response()->json([
                'title' => 'Error',
                'error' => 'Verified deposits cannot be modified.',
            ], 422);
        }

        $data = $request->validated();
        $data['is_cash'] = !empty($data['is_cash']);
        $data['base_amount'] = (float) $data['amount'];
        $data['currency'] = !empty($data['currency']) ? strtoupper($data['currency']) : 'USD';

        $deposit->update($data);

        return response()->json([
            'title' => 'Successfully',
            'successful' => 'The record was successfully updated.',
            'data' => $deposit,
        ]);
    }

    /**
     * Delete unverified deposit.
     */
    public function destroy($id): JsonResponse
    {
        $this->authorizePermission([
            'all',
            'accounting:cashDeposit:delete',
            'cash-deposit:delete',
        ]);

        $result = $this->fundTransferRepository->delete((int) $id);

        if (!$result['success']) {
            return response()->json([
                'title' => 'Error',
                'error' => $result['message'],
            ], 422);
        }

        return response()->json([
            'title' => 'Successfully',
            'successful' => $result['message'],
        ]);
    }

    /**
     * Verify deposit inside transaction and update patient balance.
     */
    public function verify($id): JsonResponse
    {
        $this->authorizePermission([
            'all',
            'accounting:cashDeposit:edit',
            'cash-deposit:edit',
            'accounting:cashDeposit:view',
            'cash-deposit:view',
        ]);

        $result = $this->fundTransferRepository->verify((int) $id);

        if (!$result['success']) {
            return response()->json([
                'title' => 'Error',
                'error' => $result['message'],
            ], 422);
        }

        return response()->json([
            'title' => 'Successfully',
            'successful' => $result['message'],
        ]);
    }

    /**
     * Get all deposit records for a given patient (patient deposit detail modal).
     */
    public function patientDetails($patient_id): JsonResponse
    {
        $this->authorizePermission([
            'all',
            'accounting:cashDeposit:view',
            'cash-deposit:view',
        ]);

        $patient = Patient::findOrFail($patient_id);
        $deposits = $this->fundTransferRepository->getFundTransferDetail((int) $patient_id);

        return response()->json([
            'patient' => [
                'id' => (int) $patient->id,
                'full_name' => $patient->full_name,
                'id_number' => $patient->id_number,
                'phone' => $patient->phone,
                'balance' => (float) ($patient->balance ?? 0),
                'balance_formatted' => number_format((float) ($patient->balance ?? 0), 2, '.', ''),
            ],
            'deposits' => $deposits,
        ]);
    }

    /**
     * Server-side patient search autocomplete for filtering / modal selection.
     */
    public function patientSearch(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));

        if (mb_strlen($search) < 2) {
            return response()->json([]);
        }

        $term = mb_strtolower($search, 'UTF-8');

        $patients = Patient::query()
            ->where(function ($query) use ($term, $search) {
                $query->whereRaw('LOWER(firstname) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, ''))) LIKE ?", ["%{$term}%"])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$term}%"]);

                if (is_numeric($search)) {
                    $query->orWhere('id_number', (int) $search)
                        ->orWhere('id', (int) $search);
                }
            })
            ->latest('id')
            ->take(15)
            ->get(['id', 'firstname', 'lastname', 'id_number', 'phone', 'balance']);

        return response()->json($patients);
    }

    /**
     * Get patient current balance.
     */
    public function getCreditBalance(Request $request): JsonResponse
    {
        $patientId = $request->query('patient_id');
        if (!$patientId) {
            return response()->json(['title' => 'Error', 'error' => 'Patient ID is required.'], 400);
        }

        $patient = Patient::find($patientId);
        if (!$patient) {
            return response()->json(['title' => 'Error', 'error' => 'Patient not found.'], 404);
        }

        $balance = (float) ($patient->balance ?? 0);

        return response()->json([
            'title' => 'Successfully',
            'successful' => 'The record was found.',
            'balance_text' => number_format($balance, 2, '.', ''),
            'balance' => $balance,
        ]);
    }

    /**
     * Print cash deposit receipt (PDF matching attached 2.pdf layout).
     */
    public function print($id): Response
    {
        $this->authorizePermission([
            'all',
            'accounting:cashDeposit:print',
            'cash-deposit:print',
            'accounting:cashDeposit:view',
            'cash-deposit:view',
        ]);

        $deposit = FundTransfer::with(['patient', 'creator', 'verifier'])->findOrFail($id);

        abort_if(
            !$deposit->is_verify,
            403,
            'Only verified deposits can be printed.'
        );

        $clinic = $this->getClinicInfo();
        $patient = $deposit->patient;

        $dateFormatted = $deposit->date
            ? Carbon::parse($deposit->date)->format('d/m/Y H:i')
            : ($deposit->created_at ? $deposit->created_at->format('d/m/Y H:i') : Carbon::now()->format('d/m/Y H:i'));

        return Inertia::render('Tenant/CashDeposit/Print', [
            'deposit' => [
                'id' => (int) $deposit->id,
                'code_formatted' => $deposit->formatted_code,
                'date_formatted' => $dateFormatted,
                'patient_fullname' => $patient ? $patient->full_name : 'N/A',
                'patient_id_number' => $patient ? $patient->id_number : null,
                'amount_formatted' => number_format((float) $deposit->amount, 2, '.', ''),
                'currency' => $deposit->currency ?: 'USD',
                'is_cash' => (bool) $deposit->is_cash,
                'note' => $deposit->note ?: '',
                'creator_name' => $deposit->creator ? $deposit->creator->name : null,
                'verifier_name' => $deposit->verifier ? $deposit->verifier->name : null,
            ],
            'clinic' => $clinic,
        ]);
    }
}
