<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\DentalChart;
use App\Models\Tenant\DentalCondition;
use App\Models\Tenant\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DentalViewController extends Controller
{
    public function index(Request $request): Response
    {
        $chartId = $request->query('chart_id');
        $activeChart = null;

        if ($chartId) {
            $activeChart = DentalChart::with(['patient', 'teeth.findings.condition'])->find($chartId);
        }

        $conditions = DentalCondition::where('is_active', true)->orderBy('name')->get();
        $patients = Patient::with(['dentalCharts' => fn ($q) => $q->latest('chart_date')])
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get();

        return Inertia::render('Tenant/DentalChart/Dental', [
            'activeChart' => $activeChart,
            'conditions' => $conditions,
            'patients' => $patients,
            'invoiceId' => $request->query('invoice_id'),
            'invoiceItemId' => $request->query('invoice_item_id'),
        ]);
    }

    public function show(DentalChart $dentalChart, Request $request): Response
    {
        $dentalChart->load(['patient', 'teeth.findings.condition']);
        $conditions = DentalCondition::where('is_active', true)->orderBy('name')->get();
        $patients = Patient::with(['dentalCharts' => fn ($q) => $q->latest('chart_date')])
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get();

        return Inertia::render('Tenant/DentalChart/Dental', [
            'activeChart' => $dentalChart,
            'conditions' => $conditions,
            'patients' => $patients,
            'invoiceId' => $request->query('invoice_id'),
            'invoiceItemId' => $request->query('invoice_item_id'),
        ]);
    }
}

