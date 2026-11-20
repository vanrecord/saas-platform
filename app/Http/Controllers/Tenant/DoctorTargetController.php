<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DoctorTargetRequest;
use App\Models\Tenant\Doctor;
use App\Repositories\TargetRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class DoctorTargetController extends Controller
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
            'target-doctor:view',
            'target:doctor:view',
            'targets.doctor.index',
            'target-doctor',
        ]);

        $targets = $this->targetRepository->getDoctorDataTable();

        if ($request->wantsJson()) {
            return response()->json($targets);
        }

        $doctors = Doctor::with(['user.profile'])
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'name' => $d->full_name ?: ($d->user?->name ?? "Doctor #{$d->id}"),
                ];
            });

        return Inertia::render('Tenant/Target/Doctor/Index', [
            'targets' => $targets,
            'doctors' => $doctors,
        ])->table(function (InertiaTable $table) {
            $this->targetRepository->columnsDoctorDataTable($table);
        });
    }

    public function store(DoctorTargetRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorizePermission([
            'all',
            'target-doctor:create',
            'target-doctor:save',
            'target:doctor:create',
            'target:doctor:save',
        ]);

        $target = $this->targetRepository->createDoctorTarget($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Doctor target created successfully.',
                'target' => $target,
            ]);
        }

        return redirect()->route('targets.doctor.index')
            ->with('success', 'Doctor target created successfully.');
    }

    public function update(DoctorTargetRequest $request, int $id): RedirectResponse|JsonResponse
    {
        $this->authorizePermission([
            'all',
            'target-doctor:edit',
            'target-doctor:save',
            'target:doctor:edit',
            'target:doctor:save',
        ]);

        $target = $this->targetRepository->updateDoctorTarget($id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Doctor target updated successfully.',
                'target' => $target,
            ]);
        }

        return redirect()->route('targets.doctor.index')
            ->with('success', 'Doctor target updated successfully.');
    }

    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->authorizePermission([
            'all',
            'target-doctor:delete',
            'target:doctor:delete',
            'target-doctor:destroy',
        ]);

        $this->targetRepository->deleteTarget($id, true);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Doctor target deleted successfully.',
            ]);
        }

        return redirect()->route('targets.doctor.index')
            ->with('success', 'Doctor target deleted successfully.');
    }
}
