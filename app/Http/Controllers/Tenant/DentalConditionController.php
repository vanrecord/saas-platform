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
    protected function authorizePermission(string $permission): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return;
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return;
        }

        if (!$user->can($permission)) {
            abort(403, "Unauthorized: missing {$permission} permission.");
        }
    }

    public function index(Request $request): Response
    {
        $this->authorizePermission('dental-condition:view');

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
        $this->authorizePermission('dental-condition:create');

        return Inertia::render('Tenant/DentalCondition/Create');
    }

    public function store(DentalConditionRequest $request): RedirectResponse
    {
        $this->authorizePermission('dental-condition:save');

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
        $this->authorizePermission('dental-condition:edit');

        return Inertia::render('Tenant/DentalCondition/Edit', [
            'dentalCondition' => $dentalCondition,
        ]);
    }

    public function update(DentalConditionRequest $request, DentalCondition $dentalCondition): RedirectResponse
    {
        $this->authorizePermission('dental-condition:save');

        $dentalCondition->update($request->validated());

        return redirect()->route('dental-conditions.index')
            ->with('success', 'Dental condition updated successfully.');
    }

    public function destroy(DentalCondition $dentalCondition): RedirectResponse
    {
        $this->authorizePermission('dental-condition:delete');

        $dentalCondition->delete();

        return redirect()->route('dental-conditions.index')
            ->with('success', 'Dental condition deleted successfully.');
    }
}

