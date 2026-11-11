<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\InvoicePrefixRequest;
use App\Repositories\InvoicePrefixRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class InvoicePrefixController extends Controller
{
    protected InvoicePrefixRepositoryInterface $prefixRepository;

    public function __construct(InvoicePrefixRepositoryInterface $prefixRepository)
    {
        $this->prefixRepository = $prefixRepository;
    }

    public function index(): Response
    {
        abort_if(!auth()->user()?->can('invoice-prefix:view'), 403, 'Unauthorized to view invoice prefixes.');

        $prefixes = $this->prefixRepository->getDataTable();

        return Inertia::render('Tenant/Setting/InvoicePrefix/Index', [
            'prefixes' => $prefixes,
        ])->table(function (InertiaTable $table) {
            $this->prefixRepository->columnsDataTable($table);
        });
    }

    public function store(InvoicePrefixRequest $request): RedirectResponse
    {
        abort_if(!auth()->user()?->can('invoice-prefix:create') && !auth()->user()?->can('invoice-prefix:save'), 403, 'Unauthorized to create invoice prefixes.');

        $this->prefixRepository->create($request->validated());

        return redirect()->route('settings.invoice-prefixes.index')
            ->with('success', 'Invoice Prefix created successfully.');
    }

    public function update($id, InvoicePrefixRequest $request): RedirectResponse
    {
        abort_if(!auth()->user()?->can('invoice-prefix:edit') && !auth()->user()?->can('invoice-prefix:save'), 403, 'Unauthorized to update invoice prefixes.');

        $this->prefixRepository->update($id, $request->validated());

        return redirect()->route('settings.invoice-prefixes.index')
            ->with('success', 'Invoice Prefix updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('invoice-prefix:delete'), 403, 'Unauthorized to delete invoice prefixes.');

        $this->prefixRepository->delete($id);

        return redirect()->route('settings.invoice-prefixes.index')
            ->with('success', 'Invoice Prefix deleted successfully.');
    }
}
