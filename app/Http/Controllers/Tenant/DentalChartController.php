<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DentalChartRequest;
use App\Models\Tenant\DentalChart;
use App\Models\Tenant\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DentalChartController extends Controller
{
    public function index(Request $request): Response
    {
        $query = DentalChart::query()
            ->with(['patient'])
            ->withCount('teeth');

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->integer('patient_id'));
        }

        $charts = $query->latest('chart_date')->paginate(15)->withQueryString();
        $patients = Patient::orderBy('firstname')->orderBy('lastname')->get(['id', 'firstname', 'lastname', 'id_number']);

        return Inertia::render('Tenant/DentalChart/Index', [
            'charts' => $charts,
            'patients' => $patients,
            'filters' => [
                'patient_id' => $request->query('patient_id'),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $selectedPatientId = $request->query('patient_id');
        $patients = Patient::orderBy('firstname')->orderBy('lastname')->get(['id', 'firstname', 'lastname', 'id_number']);

        return Inertia::render('Tenant/DentalChart/Create', [
            'patients' => $patients,
            'selectedPatientId' => $selectedPatientId ? (int)$selectedPatientId : null,
        ]);
    }

    public function store(DentalChartRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['created_by'] = $request->user()->id;
        }

        $chart = DentalChart::create($data);

        return redirect()->route('dental.show', $chart)
            ->with('success', 'Dental chart created successfully. You can now record tooth findings.');
    }

    public function show(DentalChart $dentalChart): RedirectResponse
    {
        return redirect()->route('dental.show', $dentalChart);
    }

    public function edit(DentalChart $dentalChart): Response
    {
        $dentalChart->load('patient');
        $patients = Patient::orderBy('firstname')->orderBy('lastname')->get(['id', 'firstname', 'lastname', 'id_number']);

        return Inertia::render('Tenant/DentalChart/Edit', [
            'dentalChart' => $dentalChart,
            'patients' => $patients,
        ]);
    }

    public function update(DentalChartRequest $request, DentalChart $dentalChart): RedirectResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['updated_by'] = $request->user()->id;
        }

        $dentalChart->update($data);

        return redirect()->route('dental.show', $dentalChart)
            ->with('success', 'Dental chart updated successfully.');
    }

    public function destroy(DentalChart $dentalChart): RedirectResponse
    {
        $dentalChart->delete();

        return redirect()->route('dental-charts.index')
            ->with('success', 'Dental chart deleted successfully.');
    }
}

