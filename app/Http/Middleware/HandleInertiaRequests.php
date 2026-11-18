<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Laravel\Jetstream\Jetstream;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $logo = null;
        $hospital = null;
        if (Schema::hasTable('hospitals')) {
            $hospital = \App\Models\Hospital::first();
        }

        $user = $request->user('tenant') ?? $request->user('web') ?? $request->user();

        return array_merge(parent::share($request), [
            'user' => $user,
            'hospital' => $hospital,
            'clinic_info' => fn () => $this->getTenantClinicInfo(),
            'clinicInfo' => fn () => $this->getTenantClinicInfo(),
            'tenant' => function () {
                if (!function_exists('tenant') || !tenant('id')) {
                    return null;
                }
                $info = $this->getTenantClinicInfo();
                return [
                    'id' => tenant('id'),
                    'company' => $info['title'] ?? (tenant('company') ?? tenant('name')),
                    'name_kh' => $info['titlekh'] ?? (tenant('name_kh') ?? 'គ្លីនិកធ្មេញ'),
                    'domain' => request()->getHost(),
                ];
            },
            'auth' => [
                'user' => $user,
                'can' => $user ? $user->getPermissionArray() : [],
                'canAction' => $user ? $user->can($request->route()?->getName()) : false,
                'isSuperAdmin' => optional($user)->isSuperAdmin(),
                'isAdmin' => optional($user)->isAdmin(),
            ],
            'jetstream' => [
                'hasTermsAndPrivacyPolicyFeature' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
                'managesProfilePhotos' => Jetstream::managesProfilePhotos(),
                'hasAccountDeletionFeatures' => Jetstream::hasAccountDeletionFeatures(),
                'hasApiFeatures' => Jetstream::hasApiFeatures(),
                'hasTeamFeatures' => Jetstream::hasTeamFeatures(),
                'canUpdateProfileInformation' => class_exists('Laravel\Fortify\Features') ? \Laravel\Fortify\Features::canUpdateProfileInformation() : true,
                'canUpdatePassword' => class_exists('Laravel\Fortify\Features') ? \Laravel\Fortify\Features::canUpdatePasswords() : true,
                'canManageTwoFactorAuthentication' => class_exists('Laravel\Fortify\Features') ? \Laravel\Fortify\Features::canManageTwoFactorAuthentication() : false,
                'flash' => $request->hasSession() ? $request->session()->get('flash', []) : [],
            ],
            'currency' => [],
            'appName' => config('app.name'),
            'company_logo' => $logo ? $logo->getTemporaryUrl(Carbon::now()->addMinutes(10)) : null,
        ]);
    }

    /**
     * Resolve clinic info from tenant settings and assets.
     */
    protected function getTenantClinicInfo(): ?array
    {
        if (!function_exists('tenant') || !tenant('id')) {
            return null;
        }

        try {
            if (Schema::hasTable('settings')) {
                $settings = DB::table('settings')->pluck('setting_value', 'setting_key')->toArray();
                $logoPath = $settings['logo'] ?? (tenant('logo') ?? null);
                $logoUrl = null;
                if ($logoPath) {
                    if (str_starts_with($logoPath, 'http')) {
                        $logoUrl = $logoPath;
                    } else {
                        $cleanPath = ltrim(preg_replace('/^(\/storage\/|storage\/)/i', '', $logoPath), '/');
                        if (function_exists('tenant_asset')) {
                            $logoUrl = tenant_asset($cleanPath);
                        } elseif (function_exists('assetStorage')) {
                            $logoUrl = assetStorage(Storage::disk('public')->url($cleanPath));
                        } else {
                            $logoUrl = Storage::disk('public')->url($cleanPath);
                        }
                    }
                }

                return [
                    'logo' => $logoPath,
                    'logo_url' => $logoUrl,
                    'titlekh' => $settings['titlekh'] ?? ($settings['clinic_name_kh'] ?? ($settings['shop_name'] ?? (tenant('name_kh') ?? (tenant('name_native') ?? 'គ្លីនិកធ្មេញ')))),
                    'title' => $settings['title'] ?? ($settings['clinic_name_en'] ?? ($settings['name_en'] ?? (tenant('company') ?? (tenant('name') ?? 'Dental Clinic')))),
                    'color_title_kh' => $settings['color_title_kh'] ?? '#000000',
                    'color_title_en' => $settings['color_title_en'] ?? '#000000',
                    'color_description' => $settings['color_description'] ?? '#000000',
                    'description' => $settings['description'] ?? '',
                    'phone' => $settings['phone'] ?? (tenant('phone') ?? ''),
                    'email' => $settings['email'] ?? (tenant('email') ?? ''),
                    'address' => $settings['address'] ?? (tenant('address') ?? ''),
                ];
            }
        } catch (\Throwable $e) {}

        return null;
    }
}
