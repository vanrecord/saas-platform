<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\MedicalHistory;
use App\Models\Tenant\Patient;
use App\Models\Tenant\Payment;
use App\Models\Tenant\Reception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TenantDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        abort_if(!auth()->user()?->can('dashboard:view'), 403, 'Unauthorized to view dashboard.');

        $today = Carbon::today();

        $totalPatients = Patient::count();
        $todayVisits = Reception::whereDate('reception_date', $today)->count();
        $pendingInvoices = MedicalHistory::where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', MedicalHistory::PAID);
        })->count();
        $totalRevenue = max(0, (float) (Payment::sum('amount') ?? 0));

        $recentPatients = Patient::latest()->take(6)->get();

        $todayAppointments = Reception::with(['patient', 'doctor.user.profile'])
            ->latest('reception_date')
            ->take(6)
            ->get()
            ->map(function ($rec) {
                return [
                    'id' => $rec->id,
                    'patient' => $rec->patient,
                    'visit_type' => $rec->visit_type,
                    'doctor_name' => $rec->doctor?->full_name ?: ($rec->doctor?->user?->name ?? 'Attending Dentist'),
                    'status' => $rec->status ?? 'checked_in',
                    'visit_number' => $rec->reception_number,
                ];
            });

        $totalCharts = MedicalHistory::count();

        return Inertia::render('Tenant/Dashboard', [
            'stats' => [
                'total_patients' => $totalPatients,
                'today_visits' => $todayVisits,
                'pending_invoices' => $pendingInvoices,
                'total_revenue' => number_format((float) $totalRevenue, 2),
                'total_charts' => $totalCharts,
            ],
            'recent_patients' => $recentPatients,
            'today_appointments' => $todayAppointments,
            'tenant' => [
                'name' => tenant('name') ?? 'Dental Clinic',
                'company' => tenant('company') ?? tenant('name') ?? 'Dental Clinic',
                'logo' => tenant('logo') ?? null,
            ],
        ]);
    }
}

