<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ProductRequest;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class ProductController extends Controller
{
    protected ProductRepositoryInterface $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        $this->product = $product;
    }

    public function index(): Response
    {
        abort_if(!auth()->user()?->can('product:view'), 403, 'Unauthorized to view products.');

        $products = $this->product->getDataTable();

        return Inertia::render('Tenant/Product/Index', [
            'products' => $products,
        ])->table(function (InertiaTable $table) {
            $this->product->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        abort_if(!auth()->user()?->can('product:create'), 403, 'Unauthorized to create products.');

        $suppliers = $this->product->getSuppliers();
        $categories = $this->product->getProductCategories();

        return Inertia::render('Tenant/Product/Create', [
            'suppliers' => $suppliers,
            'categories' => $categories,
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        abort_if(!auth()->user()?->can('product:save'), 403, 'Unauthorized to save products.');

        $this->product->createProduct($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show($id): Response
    {
        abort_if(!auth()->user()?->can('product:view'), 403, 'Unauthorized to view products.');

        $product = $this->product->findWithDetails((int) $id);

        if (!$product) {
            abort(404, 'Product not found');
        }

        return Inertia::render('Tenant/Product/Show', [
            'product' => $product,
        ]);
    }

    public function edit($id): Response
    {
        abort_if(!auth()->user()?->can('product:edit'), 403, 'Unauthorized to edit products.');

        $product = $this->product->findWithDetails((int) $id);

        if (!$product) {
            abort(404, 'Product not found');
        }

        $suppliers = $this->product->getSuppliers();
        $categories = $this->product->getProductCategories();

        return Inertia::render('Tenant/Product/Edit', [
            'product' => $product,
            'suppliers' => $suppliers,
            'categories' => $categories,
        ]);
    }

    public function update(ProductRequest $request, $id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('product:save'), 403, 'Unauthorized to save products.');

        $updated = $this->product->updateProduct((int) $id, $request->validated());

        if (!$updated) {
            abort(404, 'Product not found');
        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('product:delete'), 403, 'Unauthorized to delete products.');

        $deleted = $this->product->deleteProduct((int) $id);

        if (!$deleted) {
            abort(404, 'Product not found');
        }

        return redirect()->back()
            ->with('success', 'Product deleted successfully.');
    }
}
