<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\Profile;
use App\Models\Tenant\Role;
use App\Models\User;
use App\Models\UserTenant;
use App\Repositories\TenantUserRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\Permission\PermissionRegistrar;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserRepository implements TenantUserRepositoryInterface
{
    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $term = strtolower(trim((string) $value));
            $query->where(function ($query) use ($term, $value) {
                $query->where(DB::raw('LOWER(users.email)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(users.name)'), 'LIKE', "%{$term}%")
                    ->orWhereHas('profile', function ($q) use ($term) {
                        $q->where(DB::raw('LOWER(firstname)'), 'LIKE', "%{$term}%")
                            ->orWhere(DB::raw('LOWER(lastname)'), 'LIKE', "%{$term}%")
                            ->orWhere(DB::raw('LOWER(primary_mobile)'), 'LIKE', "%{$term}%");
                    })
                    ->orWhereHas('roles', function ($q) use ($term) {
                        $q->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%");
                    });

                if (is_numeric($value)) {
                    $query->orWhere('users.id', (int) $value);
                }
            });
        });

        $query = UserTenant::query()
            ->with(['profile', 'roles'])
            ->where(function ($query) {
                $query->where('is_super_admin', false)
                    ->orWhereNull('is_super_admin');
            })
            ->where(function ($query) {
                $query->whereNotIn('level_access', [UserTenant::SUPER_ADMIN, UserTenant::ADMIN, UserTenant::USER_DEV])
                    ->orWhereNull('level_access');
            });

        $users = QueryBuilder::for($query)
            ->defaultSort('-id')
            ->allowedSorts(['id', 'name', 'email', 'is_active', 'created_at'])
            ->allowedFilters([
                'name',
                'email',
                'is_active',
                AllowedFilter::callback('firstname', function ($query, $value) {
                    $term = strtolower(trim((string) $value));
                    $query->whereHas('profile', fn($q) => $q->where(DB::raw('LOWER(firstname)'), 'LIKE', "%{$term}%"));
                }),
                AllowedFilter::callback('lastname', function ($query, $value) {
                    $term = strtolower(trim((string) $value));
                    $query->whereHas('profile', fn($q) => $q->where(DB::raw('LOWER(lastname)'), 'LIKE', "%{$term}%"));
                }),
                AllowedFilter::callback('role', function ($query, $value) {
                    $term = strtolower(trim((string) $value));
                    $query->whereHas('roles', fn($q) => $q->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%"));
                }),
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        return $users;
    }

    public function columnsDataTable(InertiaTable $table): void
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();
        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'id', label: 'ID', sortable: true);
        $table->column(key: 'avatar', label: 'Avatar');
        $table->column(key: 'firstname', label: 'First Name', sortable: false, searchable: true);
        $table->column(key: 'lastname', label: 'Last Name', sortable: false, searchable: true);
        $table->column(key: 'email', label: 'Email', sortable: true, searchable: true);
        $table->column(key: 'role', label: 'Role', sortable: false, searchable: true);
        $table->column(key: 'status', label: 'Status', sortable: true, searchable: false);
    }

    public function find(int $id): ?UserTenant
    {
        return UserTenant::with(['profile', 'roles'])->find($id);
    }

    public function findWithProfile(int $id): ?UserTenant
    {
        return $this->find($id);
    }

    public function createUser(array $data, ?UploadedFile $avatar = null): UserTenant
    {
        return DB::transaction(function () use ($data, $avatar) {
            $username = trim($data['username'] ?? $data['name'] ?? '');
            $email = trim($data['email']);

            $user = UserTenant::create([
                'name' => $username,
                'email' => $email,
                'password' => Hash::make($data['password'] ?? 'password123'),
                'is_active' => isset($data['active']) ? (bool) $data['active'] : (isset($data['is_active']) ? (bool) $data['is_active'] : true),
                'level_access' => null,
                'is_super_admin' => false,
            ]);

            // Assign role
            if (!empty($data['role'])) {
                $user->syncRoles([$data['role']]);
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            }

            // Upload avatar
            $avatarPath = null;
            if ($avatar && $avatar->isValid()) {
                $ext = $avatar->getClientOriginalExtension() ?: 'png';
                $filename = 'u_' . $user->id . '_' . time() . '.' . $ext;
                $avatarPath = $avatar->storeAs('avatars', $filename, 'public');
            }

            // Format e-Health consult settings
            $consultType = $this->formatConsultType($data);

            // Create related Profile
            $user->profile()->create([
                'firstname' => $data['firstname'] ?? '',
                'lastname' => $data['lastname'] ?? '',
                'gender' => $this->normalizeGender($data['gender'] ?? 'F'),
                'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                'primary_mobile' => $data['primary_mobile'] ?? null,
                'secondary_mobile' => $data['secondary_mobile'] ?? null,
                'avatar' => $avatarPath,
                'is_publish' => !empty($data['is_publish']),
                'ehealth_is_doctor_appointment' => !empty($data['ehealth_is_doctor_appointment']),
                'ehealth_consult_type' => $consultType,
                'is_completed' => true,
            ]);

            return $user->load(['profile', 'roles']);
        });
    }

    public function updateUser(int $id, array $data, ?UploadedFile $avatar = null, bool $removeAvatar = false): ?UserTenant
    {
        return DB::transaction(function () use ($id, $data, $avatar, $removeAvatar) {
            $user = UserTenant::with(['profile', 'roles'])->find($id);
            if (!$user) {
                return null;
            }

            $username = trim($data['username'] ?? $data['name'] ?? $user->name);
            $email = trim($data['email'] ?? $user->email);

            $user->name = $username;
            $user->email = $email;
            if (isset($data['active']) || isset($data['is_active'])) {
                $user->is_active = isset($data['active']) ? (bool) $data['active'] : (bool) $data['is_active'];
            }
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->save();

            // Sync role
            if (isset($data['role'])) {
                if (!empty($data['role'])) {
                    $user->syncRoles([$data['role']]);
                } else {
                    $user->syncRoles([]);
                }
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            }

            $profile = $user->profile;
            $avatarPath = $profile?->avatar;

            // Handle avatar
            if ($avatar && $avatar->isValid()) {
                if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }
                $ext = $avatar->getClientOriginalExtension() ?: 'png';
                $filename = 'u_' . $user->id . '_' . time() . '.' . $ext;
                $avatarPath = $avatar->storeAs('avatars', $filename, 'public');
            } elseif ($removeAvatar) {
                if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }
                $avatarPath = null;
            }

            $consultType = $this->formatConsultType($data);

            $profileData = [
                'firstname' => $data['firstname'] ?? $profile?->firstname ?? '',
                'lastname' => $data['lastname'] ?? $profile?->lastname ?? '',
                'gender' => $this->normalizeGender($data['gender'] ?? $profile?->gender ?? 'F'),
                'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                'primary_mobile' => $data['primary_mobile'] ?? null,
                'secondary_mobile' => $data['secondary_mobile'] ?? null,
                'avatar' => $avatarPath,
                'is_publish' => !empty($data['is_publish']),
                'ehealth_is_doctor_appointment' => !empty($data['ehealth_is_doctor_appointment']),
                'ehealth_consult_type' => $consultType,
                'is_completed' => true,
            ];

            if ($profile) {
                $profile->update($profileData);
            } else {
                $user->profile()->create($profileData);
            }

            return $user->fresh(['profile', 'roles']);
        });
    }

    public function deleteUser(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = UserTenant::with('profile')->find($id);
            if (!$user) {
                return false;
            }

            if ($user->profile?->avatar && Storage::disk('public')->exists($user->profile->avatar)) {
                Storage::disk('public')->delete($user->profile->avatar);
            }

            $user->profile()?->delete();
            $user->roles()->detach();
            $user->delete();

            return true;
        });
    }

    public function getRoles(): array
    {
        return Role::where('guard_name', 'tenant')
            ->orderBy('name')
            ->pluck('name', 'name')
            ->toArray();
    }

    protected function formatConsultType(array $data): string
    {
        $enableVideo = isset($data['ehealth_enable_video_consult'])
            ? !empty($data['ehealth_enable_video_consult'])
            : (isset($data['consult_video']) ? !empty($data['consult_video']) : false);

        $videoFee = isset($data['ehealth_video_consult_fee'])
            ? $data['ehealth_video_consult_fee']
            : (isset($data['consult_video_fee']) ? $data['consult_video_fee'] : '');

        $enableVisit = isset($data['ehealth_enable_visit_consult'])
            ? !empty($data['ehealth_enable_visit_consult'])
            : (isset($data['consult_visit']) ? !empty($data['consult_visit']) : false);

        $visitFee = isset($data['ehealth_visit_consult_fee'])
            ? $data['ehealth_visit_consult_fee']
            : (isset($data['consult_visit_fee']) ? $data['consult_visit_fee'] : '');

        $enableHouseCall = isset($data['ehealth_enable_house_call'])
            ? !empty($data['ehealth_enable_house_call'])
            : (isset($data['consult_house']) ? !empty($data['consult_house']) : false);

        $houseCallFee = isset($data['ehealth_house_call_fee'])
            ? $data['ehealth_house_call_fee']
            : (isset($data['consult_house_fee']) ? $data['consult_house_fee'] : '');

        return json_encode([
            'enable_video' => (bool) $enableVideo,
            'video_fee' => $videoFee !== null ? $videoFee : '',
            'enable_visit' => (bool) $enableVisit,
            'visit_fee' => $visitFee !== null ? $visitFee : '',
            'enable_house_call' => (bool) $enableHouseCall,
            'house_call_fee' => $houseCallFee !== null ? $houseCallFee : '',
        ]);
    }

    protected function normalizeGender(?string $gender): string
    {
        if (empty($gender)) {
            return 'F';
        }

        $first = strtoupper(substr(trim($gender), 0, 1));
        return $first === 'M' ? 'M' : 'F';
    }
}
