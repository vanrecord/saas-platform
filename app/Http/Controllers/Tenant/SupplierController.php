<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\SupplierRequest;
use App\Repositories\SupplierRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class SupplierController extends Controller
{
    protected SupplierRepositoryInterface $supplier;

    public function __construct(SupplierRepositoryInterface $supplier)
    {
        $this->supplier = $supplier;
    }

    public function index(): Response
    {
        $suppliers = $this->supplier->getDataTable();

        return Inertia::render('Tenant/Supplier/Index', [
            'suppliers' => $suppliers,
        ])->table(function (InertiaTable $table) {
            $this->supplier->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        $provinces = $this->supplier->getProvinces();

        return Inertia::render('Tenant/Supplier/Create', [
            'provinces' => $provinces,
        ]);
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $supplier = $this->supplier->create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        $this->supplier->saveAddress($supplier, $data);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show($id): Response
    {
        $supplier = $this->supplier->findWithAddress($id);

        if (!$supplier) {
            abort(404, 'Supplier not found');
        }

        return Inertia::render('Tenant/Supplier/Show', [
            'supplier' => $supplier,
        ]);
    }

    public function edit($id): Response
    {
        $supplier = $this->supplier->findWithAddress($id);

        if (!$supplier) {
            abort(404, 'Supplier not found');
        }

        $provinces = $this->supplier->getProvinces();

        return Inertia::render('Tenant/Supplier/Edit', [
            'supplier' => $supplier,
            'provinces' => $provinces,
        ]);
    }

    public function update(SupplierRequest $request, $id): RedirectResponse
    {
        $supplier = $this->supplier->find($id);

        if (!$supplier) {
            abort(404, 'Supplier not found');
        }

        $data = $request->validated();

        $this->supplier->update($supplier, [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        $this->supplier->saveAddress($supplier, $data);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $supplier = $this->supplier->find($id);

        if ($supplier) {
            $this->supplier->delete($supplier);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function getProvinces(): JsonResponse
    {
        return response()->json($this->supplier->getProvinces());
    }

    public function getDistricts($provinceId): JsonResponse
    {
        return response()->json($this->supplier->getDistricts($provinceId));
    }

    public function getCommunes($districtId): JsonResponse
    {
        return response()->json($this->supplier->getCommunes($districtId));
    }

    public function getVillages($communeId): JsonResponse
    {
        return response()->json($this->supplier->getVillages($communeId));
    }
}
