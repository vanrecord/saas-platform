<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Repositories\CompanyRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
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
        if(Schema::hasTable('companies')) {
            $company = \App\Models\Company::first();
            $logo = !empty($company)&&!empty($company->logo)?$company->logo->first():null;
        }

        $user = $request->user('tenant') ?? $request->user('web') ?? $request->user();

        return array_merge(parent::share($request), [
            'user' => $user,
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
}
