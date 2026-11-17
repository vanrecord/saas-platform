<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\MedicineRequest;
use App\Repositories\MedicineRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class MedicineController extends Controller
{
    protected MedicineRepositoryInterface $medicine;

    public function __construct(MedicineRepositoryInterface $medicine)
    {
        $this->medicine = $medicine;
    }

    public function index(): Response
    {
        abort_if(
            !auth()->user()?->can('medicine:view') && !auth()->user()?->can('medicines:medicines:view'),
            403,
            'Unauthorized to view medicines.'
        );

        $medicines = $this->medicine->getDataTable();

        return Inertia::render('Tenant/Medicine/Index', [
            'medicines' => $medicines,
        ])->table(function (InertiaTable $table) {
            $this->medicine->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        abort_if(
            !auth()->user()?->can('medicine:create') && !auth()->user()?->can('medicines:medicines:create'),
            403,
            'Unauthorized to create medicines.'
        );

        return Inertia::render('Tenant/Medicine/Create');
    }

    public function store(MedicineRequest $request): RedirectResponse
    {
        abort_if(
            !auth()->user()?->can('medicine:create') &&
            !auth()->user()?->can('medicine:save') &&
            !auth()->user()?->can('medicines:medicines:create'),
            403,
            'Unauthorized to save medicines.'
        );

        $this->medicine->create($request->validated());

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine created successfully.');
    }

    public function edit($id): Response
    {
        abort_if(
            !auth()->user()?->can('medicine:edit') && !auth()->user()?->can('medicines:medicines:edit'),
            403,
            'Unauthorized to edit medicines.'
        );

        $medicine = $this->medicine->find($id);

        if (!$medicine) {
            abort(404, 'Medicine not found');
        }

        return Inertia::render('Tenant/Medicine/Edit', [
            'medicine' => $medicine,
        ]);
    }

    public function update(MedicineRequest $request, $id): RedirectResponse
    {
        abort_if(
            !auth()->user()?->can('medicine:edit') &&
            !auth()->user()?->can('medicine:save') &&
            !auth()->user()?->can('medicines:medicines:edit'),
            403,
            'Unauthorized to save medicines.'
        );

        $medicine = $this->medicine->find($id);

        if (!$medicine) {
            abort(404, 'Medicine not found');
        }

        $this->medicine->update($medicine, $request->validated());

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(
            !auth()->user()?->can('medicine:delete') && !auth()->user()?->can('medicines:medicines:delete'),
            403,
            'Unauthorized to delete medicines.'
        );

        $medicine = $this->medicine->find($id);

        if ($medicine) {
            $this->medicine->delete($medicine);
        }

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine deleted successfully.');
    }
}
