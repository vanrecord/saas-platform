<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ServiceRequest;
use App\Repositories\ServiceRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class ServiceController extends Controller
{
    protected ServiceRepositoryInterface $service;

    public function __construct(ServiceRepositoryInterface $service)
    {
        $this->service = $service;
    }

    public function index(): Response
    {
        abort_if(!auth()->user()?->can('service:view'), 403, 'Unauthorized to view services.');

        $services = $this->service->getDataTable();

        return Inertia::render('Tenant/Service/Index', [
            'services' => $services,
        ])->table(function (InertiaTable $table) {
            $this->service->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        abort_if(!auth()->user()?->can('service:create'), 403, 'Unauthorized to create services.');

        $categories = $this->service->getServiceCategories();

        return Inertia::render('Tenant/Service/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        abort_if(!auth()->user()?->can('service:save'), 403, 'Unauthorized to save services.');

        $this->service->createService($request->validated());

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully.');
    }

    public function show($id): Response
    {
        abort_if(!auth()->user()?->can('service:view'), 403, 'Unauthorized to view services.');

        $service = $this->service->findWithDetails((int) $id);

        if (!$service) {
            abort(404, 'Service not found');
        }

        return Inertia::render('Tenant/Service/Show', [
            'service' => $service,
        ]);
    }

    public function edit($id): Response
    {
        abort_if(!auth()->user()?->can('service:edit'), 403, 'Unauthorized to edit services.');

        $service = $this->service->findWithDetails((int) $id);

        if (!$service) {
            abort(404, 'Service not found');
        }

        $categories = $this->service->getServiceCategories();

        return Inertia::render('Tenant/Service/Edit', [
            'service' => $service,
            'categories' => $categories,
        ]);
    }

    public function update(ServiceRequest $request, $id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('service:save'), 403, 'Unauthorized to save services.');

        $updated = $this->service->updateService((int) $id, $request->validated());

        if (!$updated) {
            abort(404, 'Service not found');
        }

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('service:delete'), 403, 'Unauthorized to delete services.');

        $deleted = $this->service->deleteService((int) $id);

        if (!$deleted) {
            abort(404, 'Service not found');
        }

        return redirect()->back()
            ->with('success', 'Service deleted successfully.');
    }
}
