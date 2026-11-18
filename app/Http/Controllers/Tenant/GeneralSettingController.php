<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Doctor;
use App\Models\Tenant\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class GeneralSettingController extends Controller
{
    /**
     * Authorize user permission.
     */
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

        if (!$user->can($permission) && !$user->can('all') && !$user->can('settings:general:view')) {
            abort(403, "Unauthorized: missing {$permission} permission.");
        }
    }

    /**
     * Display the General Settings page.
     */
    public function index(Request $request): Response
    {
        $this->authorizePermission('settings:general:view');

        $allSettings = Setting::query()->pluck('setting_value', 'setting_key')->toArray();

        // Logo URL calculation
        $logoValue = $allSettings['logo'] ?? null;
        $logoUrl = null;
        if ($logoValue) {
            if (str_starts_with($logoValue, 'http')) {
                $logoUrl = $logoValue;
            } else {
                $cleanPath = ltrim(preg_replace('/^(\/storage\/|storage\/)/i', '', $logoValue), '/');
                if (function_exists('tenant_asset')) {
                    $logoUrl = tenant_asset($cleanPath);
                } elseif (function_exists('assetStorage')) {
                    $logoUrl = assetStorage(Storage::disk('public')->url($cleanPath));
                } else {
                    $logoUrl = Storage::disk('public')->url($cleanPath);
                }
            }
        }

        // Clinic info fields
        $clinicInfo = [
            'titlekh' => $allSettings['titlekh'] ?? '',
            'title' => $allSettings['title'] ?? '',
            'description' => $allSettings['description'] ?? '',
            'color_title_kh' => !empty($allSettings['color_title_kh']) ? $allSettings['color_title_kh'] : '#000000',
            'color_title_en' => !empty($allSettings['color_title_en']) ? $allSettings['color_title_en'] : '#000000',
            'color_description' => !empty($allSettings['color_description']) ? $allSettings['color_description'] : '#000000',
            'fbuser' => $allSettings['fbuser'] ?? '',
            'fbpage' => $allSettings['fbpage'] ?? '',
            'email' => $allSettings['email'] ?? '',
            'phone' => $allSettings['phone'] ?? '',
            'address' => $allSettings['address'] ?? '',
            'note' => $allSettings['note'] ?? '',
            'term' => $allSettings['term'] ?? '',
            'logo' => $logoValue,
            'logo_url' => $logoUrl,
        ];

        // Doctor list for Tab 2
        $doctors = Doctor::with(['user.profile', 'specialties'])
            ->get()
            ->map(function ($doc) {
                $profile = $doc->user?->profile;
                $name = $profile?->full_name;
                if (empty($name)) {
                    $name = trim(($profile?->firstname ?? '') . ' ' . ($profile?->lastname ?? ''));
                }
                if (empty($name)) {
                    $name = $doc->user?->name ?? ('Doctor #' . $doc->id);
                }

                return [
                    'id' => (int) $doc->id,
                    'name' => $name,
                    'specialty' => $doc->specialties->pluck('name')->implode(', ') ?: null,
                    'avatar_url' => $profile?->avatar_url ?? null,
                ];
            });

        // Doctor exclusion list
        $doctorExceptRaw = $allSettings['doctor_except'] ?? '[]';
        $doctorExcept = is_string($doctorExceptRaw) ? json_decode($doctorExceptRaw, true) : (array) $doctorExceptRaw;
        if (!is_array($doctorExcept)) {
            $doctorExcept = [];
        }
        $doctorExcept = array_values(array_map('intval', $doctorExcept));

        return Inertia::render('Tenant/Setting/General/Index', [
            'clinicInfo' => $clinicInfo,
            'doctors' => $doctors,
            'doctorExcept' => $doctorExcept,
            'initialTab' => $request->query('tab', 'clinic'),
        ]);
    }

    /**
     * Update clinic info settings.
     */
    public function updateClinicInfo(Request $request): RedirectResponse
    {
        $this->authorizePermission('settings:general:edit');

        $request->validate([
            'titlekh' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color_title_kh' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'color_title_en' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'color_description' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'fbuser' => 'nullable|string|max:255',
            'fbpage' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'term' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'remove_logo' => 'nullable|boolean',
        ]);

        $keys = [
            'titlekh',
            'title',
            'description',
            'color_title_kh',
            'color_title_en',
            'color_description',
            'fbuser',
            'fbpage',
            'email',
            'phone',
            'address',
            'note',
            'term',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key, ''));
            }
        }

        // Handle logo file or removal
        $oldLogo = Setting::get('logo');

        if ($request->boolean('remove_logo')) {
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::forget('logo');
        } elseif ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $file = $request->file('logo');
            $extension = strtolower($file->getClientOriginalExtension());
            $newFileName = md5(date('YmdHis') . $file->getClientOriginalName()) . '.' . $extension;
            $path = $file->storeAs('logos', $newFileName, 'public');

            if ($path) {
                if ($oldLogo && Storage::disk('public')->exists($oldLogo) && $oldLogo !== $path) {
                    Storage::disk('public')->delete($oldLogo);
                }
                Setting::set('logo', $path);
            }
        }

        return redirect()->route('settings.general.index', ['tab' => 'clinic'])
            ->with('success', 'Clinic information updated successfully.');
    }

    /**
     * Update doctor income exclusion settings.
     */
    public function updateDoctorExcept(Request $request): RedirectResponse
    {
        $this->authorizePermission('settings:general:edit');

        $request->validate([
            'doctor_except' => 'nullable|array',
            'doctor_except.*' => 'integer|exists:doctors,id',
        ]);

        $doctorExcept = array_values(array_map('intval', $request->input('doctor_except', [])));

        Setting::set('doctor_except', json_encode($doctorExcept));

        return redirect()->route('settings.general.index', ['tab' => 'income'])
            ->with('success', 'Doctor income exclusions updated successfully.');
    }
}
