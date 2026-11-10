<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\Doctor;
use App\Models\Tenant\Profile;
use App\Models\Tenant\Role;
use App\Models\Tenant\Specialty;
use App\Models\UserTenant;
use App\Repositories\DoctorRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\Permission\PermissionRegistrar;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class DoctorRepository implements DoctorRepositoryInterface
{
    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $term = strtolower(trim((string) $value));
            $query->where(function ($query) use ($term, $value) {
                $query->whereHas('user', function ($uq) use ($term) {
                    $uq->where(DB::raw('LOWER(users.email)'), 'LIKE', "%{$term}%")
                        ->orWhere(DB::raw('LOWER(users.name)'), 'LIKE', "%{$term}%")
                        ->orWhereHas('profile', function ($pq) use ($term) {
                            $pq->where(DB::raw('LOWER(firstname)'), 'LIKE', "%{$term}%")
                                ->orWhere(DB::raw('LOWER(lastname)'), 'LIKE', "%{$term}%")
                                ->orWhere(DB::raw("LOWER(CONCAT(firstname, ' ', lastname))"), 'LIKE', "%{$term}%")
                                ->orWhere(DB::raw('LOWER(gender)'), 'LIKE', "%{$term}%")
                                ->orWhere(DB::raw('LOWER(primary_mobile)'), 'LIKE', "%{$term}%");
                        });
                });

                if (is_numeric($value)) {
                    $query->orWhere('doctors.id', (int) $value);
                }
            });
        });

        $doctors = QueryBuilder::for(Doctor::query()->with(['user.profile', 'specialties']))
            ->defaultSort('-id')
            ->allowedSorts([
                'id',
                AllowedSort::callback('fullname', function ($query, $descending) {
                    $direction = $descending ? 'DESC' : 'ASC';
                    $query->leftJoin('users', 'doctors.user_id', '=', 'users.id')
                        ->leftJoin('profiles', function ($join) {
                            $join->on('profiles.profileable_id', '=', 'users.id')
                                ->where('profiles.profileable_type', '=', UserTenant::class);
                        })
                        ->orderBy(DB::raw("CONCAT(profiles.firstname, ' ', profiles.lastname)"), $direction)
                        ->select('doctors.*');
                }),
                AllowedSort::callback('email', function ($query, $descending) {
                    $direction = $descending ? 'DESC' : 'ASC';
                    $query->leftJoin('users', 'doctors.user_id', '=', 'users.id')
                        ->orderBy('users.email', $direction)
                        ->select('doctors.*');
                }),
                AllowedSort::callback('gender', function ($query, $descending) {
                    $direction = $descending ? 'DESC' : 'ASC';
                    $query->leftJoin('users', 'doctors.user_id', '=', 'users.id')
                        ->leftJoin('profiles', function ($join) {
                            $join->on('profiles.profileable_id', '=', 'users.id')
                                ->where('profiles.profileable_type', '=', UserTenant::class);
                        })
                        ->orderBy('profiles.gender', $direction)
                        ->select('doctors.*');
                }),
            ])
            ->allowedFilters([
                AllowedFilter::callback('id', function ($query, $value) {
                    if (is_numeric($value)) {
                        $query->where('doctors.id', (int) $value);
                    }
                }),
                AllowedFilter::callback('fullname', function ($query, $value) {
                    $term = strtolower(trim((string) $value));
                    $query->whereHas('user.profile', function ($q) use ($term) {
                        $q->where(DB::raw('LOWER(firstname)'), 'LIKE', "%{$term}%")
                            ->orWhere(DB::raw('LOWER(lastname)'), 'LIKE', "%{$term}%")
                            ->orWhere(DB::raw("LOWER(CONCAT(firstname, ' ', lastname))"), 'LIKE', "%{$term}%");
                    });
                }),
                AllowedFilter::callback('email', function ($query, $value) {
                    $term = strtolower(trim((string) $value));
                    $query->whereHas('user', function ($q) use ($term) {
                        $q->where(DB::raw('LOWER(email)'), 'LIKE', "%{$term}%");
                    });
                }),
                AllowedFilter::callback('gender', function ($query, $value) {
                    $term = strtolower(trim((string) $value));
                    $query->whereHas('user.profile', function ($q) use ($term) {
                        $q->where(DB::raw('LOWER(gender)'), 'LIKE', "%{$term}%");
                    });
                }),
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        return $doctors;
    }

    public function columnsDataTable(InertiaTable $table): void
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();
        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'id', label: 'ID', sortable: true);
        $table->column(key: 'avatar', label: 'Avatar');
        $table->column(key: 'fullname', label: 'Fullname', sortable: true, searchable: true);
        $table->column(key: 'email', label: 'Email', sortable: true, searchable: true);
        $table->column(key: 'gender', label: 'Gender', sortable: true, searchable: true);
    }

    public function find(int $id): ?Doctor
    {
        return Doctor::with(['user.profile', 'specialties'])->find($id);
    }

    public function findWithRelations(int $id): ?Doctor
    {
        return $this->find($id);
    }

    public function createDoctor(array $data, ?UploadedFile $avatar = null): Doctor
    {
        return DB::transaction(function () use ($data, $avatar) {
            $username = trim($data['username'] ?? '');

            // Fallback email if empty (since users.email cannot be null in database)
            $email = !empty($data['email']) ? trim($data['email']) : (Str::slug($username, '') . '@doctor.local');

            // Find or create Doctor role for tenant guard
            $doctorRole = Role::firstOrCreate(
                ['name' => 'Doctor', 'guard_name' => 'tenant'],
                ['display_name' => 'Doctor', 'description' => 'Doctor role']
            );

            // Create UserTenant
            $user = UserTenant::create([
                'name' => $username,
                'email' => $email,
                'password' => Hash::make($data['password'] ?? 'password123'),
                'is_active' => true,
                'level_access' => null,
                'is_super_admin' => false,
            ]);

            // Assign role
            $user->syncRoles([$doctorRole]);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            // Create Doctor record
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'hospital_id' => $user->hospital_id ?? 1,
            ]);

            // Sync specialties
            if (!empty($data['specialties']) && is_array($data['specialties'])) {
                $doctor->specialties()->sync($data['specialties']);
            }

            // Store avatar if provided
            $avatarPath = null;
            if ($avatar && $avatar->isValid()) {
                $ext = $avatar->getClientOriginalExtension() ?: 'png';
                $filename = 'doc_' . $doctor->id . '_' . time() . '.' . $ext;
                $avatarPath = $avatar->storeAs('avatars', $filename, 'public');
            }

            // Create Profile
            $user->profile()->create([
                'firstname' => $data['firstname'] ?? '',
                'lastname' => $data['lastname'] ?? '',
                'gender' => $this->normalizeGender($data['gender'] ?? 'F'),
                'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                'primary_mobile' => $data['primary_mobile'] ?? null,
                'secondary_mobile' => $data['secondary_mobile'] ?? null,
                'description' => $data['description'] ?? null,
                'avatar' => $avatarPath,
                'is_completed' => true,
                'is_publish' => true,
            ]);

            return $doctor->load(['user.profile', 'specialties']);
        });
    }

    public function updateDoctor(Doctor $doctor, array $data, ?UploadedFile $avatar = null): Doctor
    {
        return DB::transaction(function () use ($doctor, $data, $avatar) {
            $user = $doctor->user;

            if ($user) {
                $userUpdates = [];
                if (isset($data['username'])) {
                    $userUpdates['name'] = trim($data['username']);
                }
                if (!empty($data['email'])) {
                    $userUpdates['email'] = trim($data['email']);
                }
                if (!empty($data['password'])) {
                    $userUpdates['password'] = Hash::make($data['password']);
                }
                if (!empty($userUpdates)) {
                    $user->update($userUpdates);
                }
            }

            // Sync specialties
            if (isset($data['specialties']) && is_array($data['specialties'])) {
                $doctor->specialties()->sync($data['specialties']);
            }

            // Profile update
            $profile = $user?->profile;
            $removeAvatar = !empty($data['remove_avatar']);
            $avatarPath = $profile?->avatar;

            if ($removeAvatar) {
                if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }
                $avatarPath = null;
            }

            if ($avatar && $avatar->isValid()) {
                if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }
                $ext = $avatar->getClientOriginalExtension() ?: 'png';
                $filename = 'doc_' . $doctor->id . '_' . time() . '.' . $ext;
                $avatarPath = $avatar->storeAs('avatars', $filename, 'public');
            }

            $profileData = [
                'firstname' => $data['firstname'] ?? ($profile?->firstname ?? ''),
                'lastname' => $data['lastname'] ?? ($profile?->lastname ?? ''),
                'gender' => $this->normalizeGender($data['gender'] ?? ($profile?->gender ?? 'F')),
                'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                'primary_mobile' => $data['primary_mobile'] ?? null,
                'secondary_mobile' => $data['secondary_mobile'] ?? null,
                'description' => $data['description'] ?? null,
                'avatar' => $avatarPath,
            ];

            if ($profile) {
                $profile->update($profileData);
            } elseif ($user) {
                $user->profile()->create(array_merge($profileData, ['is_completed' => true]));
            }

            return $doctor->load(['user.profile', 'specialties']);
        });
    }

    public function deleteDoctor(Doctor $doctor): bool
    {
        return DB::transaction(function () use ($doctor) {
            $user = $doctor->user;

            if ($user && $user->profile?->avatar && Storage::disk('public')->exists($user->profile->avatar)) {
                Storage::disk('public')->delete($user->profile->avatar);
            }

            $doctor->specialties()->detach();
            $doctor->delete();

            if ($user) {
                $user->profile()?->delete();
                $user->delete();
            }

            return true;
        });
    }

    public function getSpecialties(): array
    {
        return Specialty::select('id', 'name', 'code', 'description')
            ->orderBy('name')
            ->get()
            ->toArray();
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
