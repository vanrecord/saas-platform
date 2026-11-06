<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DentalConditionRequest;
use App\Models\Tenant\DentalCondition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DentalConditionController extends Controller
{
    public function index(Request $request): Response
    {
        $conditions = DentalCondition::query()
            ->withCount('findings')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Tenant/DentalCondition/Index', [
            'conditions' => $conditions,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/DentalCondition/Create');
    }

    public function store(DentalConditionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['created_by'] = $request->user()->id;
        }

        DentalCondition::create($data);

        return redirect()->route('dental-conditions.index')
            ->with('success', 'Dental condition created successfully.');
    }

    public function edit(DentalCondition $dentalCondition): Response
    {
        return Inertia::render('Tenant/DentalCondition/Edit', [
            'dentalCondition' => $dentalCondition,
        ]);
    }

    public function update(DentalConditionRequest $request, DentalCondition $dentalCondition): RedirectResponse
    {
        $dentalCondition->update($request->validated());

        return redirect()->route('dental-conditions.index')
            ->with('success', 'Dental condition updated successfully.');
    }

    public function destroy(DentalCondition $dentalCondition): RedirectResponse
    {
        $dentalCondition->delete();

        return redirect()->route('dental-conditions.index')
            ->with('success', 'Dental condition deleted successfully.');
    }
}

