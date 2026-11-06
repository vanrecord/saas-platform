<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenancyBootstrapped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Schema\Blueprint;

class AppServiceProvider extends ServiceProvider
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
        Blueprint::macro('nestedSet', function () {
            /** @var Blueprint $this */
            $this->unsignedBigInteger('parent_id')->nullable()->index();
            $this->integer('left')->nullable()->index();
            $this->integer('right')->nullable()->index();
            $this->integer('depth')->nullable();
        });

        Blueprint::macro('dropNestedSet', function () {
            /** @var Blueprint $this */
            $this->dropColumn(['parent_id', 'left', 'right', 'depth']);
        });

        Model::preventLazyLoading(! app()->isProduction());
        Event::listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            config(['permission.cache.key' => 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->id]);
            app(\Spatie\Permission\PermissionRegistrar::class)->cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->id;
        });
        $this->eHealthApiRouteConfig();
    }

    public function eHealthApiRouteConfig(){
        Http::macro('ehealth', function () {
            return Http::withHeaders(config('ehealth.header'))
                        ->withToken(config('ehealth.token'))
                        ->baseUrl(config('ehealth.ehealth_api_url'));
        });
    }
}
