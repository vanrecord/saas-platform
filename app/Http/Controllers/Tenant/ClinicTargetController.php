<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ClinicTargetRequest;
use App\Repositories\TargetRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class ClinicTargetController extends Controller
{
    protected TargetRepositoryInterface $targetRepository;

    public function __construct(TargetRepositoryInterface $targetRepository)
    {
        $this->targetRepository = $targetRepository;
    }

    protected function authorizePermission(array|string $permissions): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->can('all') || $user->is_super_admin) {
            return;
        }

        $perms = (array) $permissions;
        foreach ($perms as $perm) {
            if ($user->can($perm)) {
                return;
            }
        }

        abort(403, 'Unauthorized to perform this action.');
    }

    public function index(Request $request): Response|JsonResponse
    {
        $this->authorizePermission([
            'all',
            'target-clinic:view',
            'target:clinic:view',
            'targets.clinic.index',
            'target-clinic',
        ]);

        $targets = $this->targetRepository->getClinicDataTable();

        if ($request->wantsJson()) {
            return response()->json($targets);
        }

        return Inertia::render('Tenant/Target/Clinic/Index', [
            'targets' => $targets,
        ])->table(function (InertiaTable $table) {
            $this->targetRepository->columnsClinicDataTable($table);
        });
    }

    public function store(ClinicTargetRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorizePermission([
            'all',
            'target-clinic:create',
            'target-clinic:save',
            'target:clinic:create',
            'target:clinic:save',
        ]);

        $target = $this->targetRepository->createClinicTarget($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Clinic target created successfully.',
                'target' => $target,
            ]);
        }

        return redirect()->route('targets.clinic.index')
            ->with('success', 'Clinic target created successfully.');
    }

    public function update(ClinicTargetRequest $request, int $id): RedirectResponse|JsonResponse
    {
        $this->authorizePermission([
            'all',
            'target-clinic:edit',
            'target-clinic:save',
            'target:clinic:edit',
            'target:clinic:save',
        ]);

        $target = $this->targetRepository->updateClinicTarget($id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Clinic target updated successfully.',
                'target' => $target,
            ]);
        }

        return redirect()->route('targets.clinic.index')
            ->with('success', 'Clinic target updated successfully.');
    }

    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->authorizePermission([
            'all',
            'target-clinic:delete',
            'target:clinic:delete',
            'target-clinic:destroy',
        ]);

        $this->targetRepository->deleteTarget($id, false);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Clinic target deleted successfully.',
            ]);
        }

        return redirect()->route('targets.clinic.index')
            ->with('success', 'Clinic target deleted successfully.');
    }
}
