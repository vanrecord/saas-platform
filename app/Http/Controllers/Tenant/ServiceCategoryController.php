<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CategoryRequest;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class ServiceCategoryController extends Controller
{
    protected CategoryRepositoryInterface $category;

    public function __construct(CategoryRepositoryInterface $category)
    {
        $this->category = $category;
    }

    public function index(): Response
    {
        abort_if(!auth()->user()?->can('service-category:view'), 403, 'Unauthorized to view service categories.');

        $categories = $this->category->getDataTable('service');

        return Inertia::render('Tenant/ServiceCategory/Index', [
            'categories' => $categories,
        ])->table(function (InertiaTable $table) {
            $this->category->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        abort_if(!auth()->user()?->can('service-category:create'), 403, 'Unauthorized to create service categories.');

        return Inertia::render('Tenant/ServiceCategory/Create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        abort_if(!auth()->user()?->can('service-category:save'), 403, 'Unauthorized to save service categories.');

        $this->category->createForType('service', $request->validated());

        return redirect()->route('service-categories.index')
            ->with('success', 'Service category created successfully.');
    }

    public function edit($id): Response
    {
        abort_if(!auth()->user()?->can('service-category:edit'), 403, 'Unauthorized to edit service categories.');

        $category = $this->category->findForType((int) $id, 'service');

        if (!$category) {
            abort(404, 'Service category not found');
        }

        return Inertia::render('Tenant/ServiceCategory/Edit', [
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, $id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('service-category:save'), 403, 'Unauthorized to save service categories.');

        $updated = $this->category->updateForType((int) $id, 'service', $request->validated());

        if (!$updated) {
            abort(404, 'Service category not found');
        }

        return redirect()->route('service-categories.index')
            ->with('success', 'Service category updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('service-category:delete'), 403, 'Unauthorized to delete service categories.');

        $deleted = $this->category->deleteForType((int) $id, 'service');

        if (!$deleted) {
            abort(404, 'Service category not found');
        }

        return redirect()->back()
            ->with('success', 'Service category deleted successfully.');
    }
}
