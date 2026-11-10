<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UserRequest;
use App\Repositories\TenantUserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class UserController extends Controller
{
    protected TenantUserRepositoryInterface $userRepository;

    public function __construct(TenantUserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(): Response
    {
        $this->authorizePermission('user:view');

        $users = $this->userRepository->getDataTable();

        return Inertia::render('Tenant/User/Index', [
            'users' => $users,
        ])->table(function (InertiaTable $table) {
            $this->userRepository->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        $this->authorizePermission('user:create');

        $roles = $this->userRepository->getRoles();

        return Inertia::render('Tenant/User/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->authorizePermission('user:save');

        $this->userRepository->createUser(
            $request->validated(),
            $request->file('avatar')
        );

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit($id): Response
    {
        $this->authorizePermission('user:edit');

        $user = $this->userRepository->findWithProfile((int) $id);

        if (!$user) {
            abort(404, 'User not found.');
        }

        $roles = $this->userRepository->getRoles();

        $profile = $user->profile;
        $consultType = $profile?->ehealth_consult_type;
        if (is_string($consultType)) {
            $consultType = json_decode($consultType, true) ?: [];
        }
        if (!is_array($consultType)) {
            $consultType = [];
        }

        $enableVideo = !empty($consultType['enable_video']) || !empty($consultType['video_consult']['enabled']) || in_array('video_consult', $consultType['types'] ?? []);
        $videoFee = $consultType['video_fee'] ?? $consultType['video_consult']['fee'] ?? $consultType['fees']['video_consult'] ?? '';

        $enableVisit = !empty($consultType['enable_visit']) || !empty($consultType['visit_consult']['enabled']) || in_array('visit_consult', $consultType['types'] ?? []);
        $visitFee = $consultType['visit_fee'] ?? $consultType['visit_consult']['fee'] ?? $consultType['fees']['visit_consult'] ?? '';

        $enableHouse = !empty($consultType['enable_house_call']) || !empty($consultType['house_call']['enabled']) || in_array('house_call', $consultType['types'] ?? []);
        $houseFee = $consultType['house_call_fee'] ?? $consultType['house_call']['fee'] ?? $consultType['fees']['house_call'] ?? '';

        $userData = [
            'id' => $user->id,
            'username' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->first()?->name ?? null,
            'active' => (bool) ($user->is_active ?? true),
            'is_publish' => (bool) ($profile?->is_publish ?? false),
            'ehealth_is_doctor_appointment' => (bool) ($profile?->ehealth_is_doctor_appointment ?? false),
            'consult_video' => (bool) $enableVideo,
            'consult_video_fee' => (string) $videoFee,
            'consult_visit' => (bool) $enableVisit,
            'consult_visit_fee' => (string) $visitFee,
            'consult_house' => (bool) $enableHouse,
            'consult_house_fee' => (string) $houseFee,
            'ehealth_enable_video_consult' => (bool) $enableVideo,
            'ehealth_video_consult_fee' => (string) $videoFee,
            'ehealth_enable_visit_consult' => (bool) $enableVisit,
            'ehealth_visit_consult_fee' => (string) $visitFee,
            'ehealth_enable_house_call' => (bool) $enableHouse,
            'ehealth_house_call_fee' => (string) $houseFee,
            'firstname' => $profile?->firstname ?? '',
            'lastname' => $profile?->lastname ?? '',
            'gender' => !empty($profile?->gender) ? (strtoupper(substr($profile->gender, 0, 1)) === 'M' ? 'M' : 'F') : 'F',
            'date_of_birth' => $profile?->date_of_birth?->format('Y-m-d') ?? ($profile?->date_of_birth ?? ''),
            'primary_mobile' => $profile?->primary_mobile ?? '',
            'secondary_mobile' => $profile?->secondary_mobile ?? '',
            'avatar_url' => $profile?->avatar_url,
        ];

        return Inertia::render('Tenant/User/Edit', [
            'user' => $userData,
            'roles' => $roles,
        ]);
    }

    public function update(UserRequest $request, $id): RedirectResponse
    {
        $this->authorizePermission('user:save');

        $updated = $this->userRepository->updateUser(
            (int) $id,
            $request->validated(),
            $request->file('avatar'),
            $request->boolean('remove_avatar')
        );

        if (!$updated) {
            abort(404, 'User not found.');
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $this->authorizePermission('user:delete');

        $currentUserId = auth()->user()?->id ?? auth('tenant')->user()?->id;
        if ($currentUserId === (int) $id) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own user account.');
        }

        $deleted = $this->userRepository->deleteUser((int) $id);

        if (!$deleted) {
            abort(404, 'User not found.');
        }

        return redirect()->back()
            ->with('success', 'User deleted successfully.');
    }

    protected function authorizePermission(string $permission): void
    {
        $user = auth()->user() ?? auth('tenant')->user();
        abort_if(!$user || !$user->can($permission), 403, "Unauthorized access to {$permission}.");
    }
}
