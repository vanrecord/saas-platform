<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CategoryRequest;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class ProductCategoryController extends Controller
{
    protected CategoryRepositoryInterface $category;

    public function __construct(CategoryRepositoryInterface $category)
    {
        $this->category = $category;
    }

    public function index(): Response
    {
        abort_if(!auth()->user()?->can('product-category:view'), 403, 'Unauthorized to view product categories.');

        $categories = $this->category->getDataTable('product');

        return Inertia::render('Tenant/ProductCategory/Index', [
            'categories' => $categories,
        ])->table(function (InertiaTable $table) {
            $this->category->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        abort_if(!auth()->user()?->can('product-category:create'), 403, 'Unauthorized to create product categories.');

        return Inertia::render('Tenant/ProductCategory/Create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        abort_if(!auth()->user()?->can('product-category:save'), 403, 'Unauthorized to save product categories.');

        $this->category->createForType('product', $request->validated());

        return redirect()->route('product-categories.index')
            ->with('success', 'Product category created successfully.');
    }

    public function edit($id): Response
    {
        abort_if(!auth()->user()?->can('product-category:edit'), 403, 'Unauthorized to edit product categories.');

        $category = $this->category->findForType((int) $id, 'product');

        if (!$category) {
            abort(404, 'Product category not found');
        }

        return Inertia::render('Tenant/ProductCategory/Edit', [
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, $id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('product-category:save'), 403, 'Unauthorized to save product categories.');

        $updated = $this->category->updateForType((int) $id, 'product', $request->validated());

        if (!$updated) {
            abort(404, 'Product category not found');
        }

        return redirect()->route('product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('product-category:delete'), 403, 'Unauthorized to delete product categories.');

        $deleted = $this->category->deleteForType((int) $id, 'product');

        if (!$deleted) {
            abort(404, 'Product category not found');
        }

        return redirect()->back()
            ->with('success', 'Product category deleted successfully.');
    }
}
