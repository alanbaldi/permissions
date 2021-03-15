<?php

namespace Lamplighter\Permissions;

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

        $this->app->make(LamplighterPermissionsController::class);
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
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
        $this->publishes([
            __DIR__.'/config/permissions.php' => config_path('permissions.php')
        ],'permissions-config');

        $this->publishes([
            __DIR__.'/migrations/' => database_path('migrations')
        ],'permissions-migrations');

        if($this->app->runningInConsole()){
            $this->commands([
                PermissionsGenerate::class
            ]);
        }
    }
}
