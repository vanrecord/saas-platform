<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\DentalCondition;
use App\Models\Tenant\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DentalViewController extends Controller
{
    public function index(Request $request): Response
    {
        $conditions = DentalCondition::where('is_active', true)->orderBy('name')->get();
        $patients = Patient::with(['medicalHistories' => fn ($q) => $q->latest('date')])
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get();

        return Inertia::render('Tenant/DentalChart/Dental', [
            'activeChart' => null,
            'conditions' => $conditions,
            'patients' => $patients,
            'invoiceId' => $request->query('invoice_id'),
            'invoiceItemId' => $request->query('invoice_item_id'),
        ]);
    }

    public function show($dentalChart, Request $request): Response
    {
        $conditions = DentalCondition::where('is_active', true)->orderBy('name')->get();
        $patients = Patient::with(['medicalHistories' => fn ($q) => $q->latest('date')])
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get();

        return Inertia::render('Tenant/DentalChart/Dental', [
            'activeChart' => null,
            'conditions' => $conditions,
            'patients' => $patients,
            'invoiceId' => $request->query('invoice_id'),
            'invoiceItemId' => $request->query('invoice_item_id'),
        ]);
    }
}
