<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Hash;
use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        Fortify::loginView(function () {
            return Inertia::render('Auth/Login',[
                'tenant'    => fn () => tenant("id")?true:false,
                'home_page' => fn () => rescue(fn () => json_decode(Http::timeout(2)->get(config("frontend-sites.app_frontend_api_url").'/home')->json()), null, false),
                'image_url' => config("frontend-sites.app_frontend_image_url"),
            ]);
        });

        Fortify::registerView(function () {
            return Inertia::render('Auth/Register',[
                'home_page' => fn () => rescue(fn () => json_decode(Http::timeout(2)->get(config("frontend-sites.app_frontend_api_url").'/home')->json()), null, false),
                'image_url' => config("frontend-sites.app_frontend_image_url"),
            ]);
        });

        $this->app->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
        $this->app->singleton(
            \Laravel\Fortify\Contracts\RegisterResponse::class,
            \App\Http\Responses\RegisterResponse::class,
        );

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where(Fortify::username(), $request->email)->first();
    
           if ($user && Hash::check($request->password, $user->password)) {
                if ($user->is_active == 1) {  // it will return if status == 1
                     return $user;
                }
           }
    
        });
    }

    /**
     * Configure the permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
