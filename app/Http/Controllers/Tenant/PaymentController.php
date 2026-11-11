<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Payment;
use App\Models\Tenant\PaymentMethod;
use App\Repositories\PaymentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class PaymentController extends Controller
{
    protected PaymentRepositoryInterface $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function index(Request $request): Response
    {
        abort_if(!auth()->user()?->can('payment:view'), 403, 'Unauthorized to view payments.');

        $payments = $this->paymentRepository->getDataTable();

        $paymentMethods = PaymentMethod::where('is_activate', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'name', 'provider', 'currency']);

        return Inertia::render('Tenant/Payment/Index', [
            'payments' => $payments,
            'paymentMethods' => $paymentMethods,
        ])->table(function (InertiaTable $table) {
            $this->paymentRepository->columnsDataTable($table);
        });
    }

    public function searchPatients(Request $request): JsonResponse
    {
        abort_if(!auth()->user()?->can('payment:view'), 403, 'Unauthorized.');

        $query = trim((string) $request->input('q', ''));

        // Strictly enforce minimum 3 characters
        if (mb_strlen($query) < 3) {
            return response()->json([]);
        }

        $patients = $this->paymentRepository->searchPatients($query);

        return response()->json($patients);
    }

    public function printReceipt($id): Response
    {
        abort_if(!auth()->user()?->can('payment:print'), 403, 'Unauthorized to print payments.');

        $payment = $this->paymentRepository->findForReceipt($id);

        $hospital = null;
        if (function_exists('tenant') && tenant()) {
            $hospital = \Illuminate\Support\Facades\DB::table('hospitals')->first();
        }

        return Inertia::render('Tenant/Payment/Receipt', [
            'payment' => $payment,
            'hospital' => $hospital,
        ]);
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('payment:delete'), 403, 'Unauthorized to delete payments.');

        $result = $this->paymentRepository->deletePayment((int) $id);

        if (!$result['success']) {
            return redirect()->back()->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('tenant.payments.index')
            ->with('success', $result['message']);
    }
}
