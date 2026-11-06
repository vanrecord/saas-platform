<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\DentalChart;
use App\Models\Tenant\Patient;
use App\Models\Tenant\PatientVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TenantDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $today = Carbon::today();

        $totalPatients = Patient::count();
        $todayVisits = PatientVisit::whereDate('visited_at', $today)->count();
        $pendingInvoices = PatientVisit::where('outstanding_balance', '>', 0)
            ->orWhere('workflow_option', 'new_invoice')
            ->count();
        $totalRevenue = PatientVisit::sum('outstanding_balance');

        $recentPatients = Patient::latest()->take(6)->get();

        $todayAppointments = PatientVisit::with('patient')
            ->latest('visited_at')
            ->take(6)
            ->get();

        $totalCharts = DentalChart::count();

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
                'company' => tenant('company') ?? tenant('name') ?? 'Dental Clinic • OdontoCare',
                'logo' => tenant('logo') ?? null,
            ],
        ]);
    }
}

