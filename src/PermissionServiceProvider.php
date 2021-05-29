<?php

namespace Lamplighter\Permissions;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        // $this->app->make(LamplighterPermissionsController::class);
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('permissions', PermissionsMiddleware::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->publishes([
            __DIR__.'/config/permissions.php' => config_path('permissions.php')
        ],'permissions-config');

        $this->publishes([
            __DIR__.'/migrations/' => database_path('migrations')
        ],'permissions-migrations');

        if($this->app->runningInConsole()){
            $this->commands([
                PermissionsInstall::class,
                PermissionsGenerate::class,
                PermissionsMakeGroups::class,
            ]);
        }
    }
}
