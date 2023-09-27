<?php

namespace Roghumi\Press\Crud\Providers;

use Illuminate\Support\ServiceProvider;
use Roghumi\Press\Crud\Commands\MakeComposite;
use Roghumi\Press\Crud\Helpers\MigrationPublishTrait;
use Roghumi\Press\Crud\Services\AccessService\AccessService;
use Roghumi\Press\Crud\Services\AccessService\IAccessService;
use Roghumi\Press\Crud\Services\CrudService\CrudService;
use Roghumi\Press\Crud\Services\CrudService\ICrudService;
use Roghumi\Press\Crud\Services\DomainService\DomainService;
use Roghumi\Press\Crud\Services\DomainService\IDomainService;
use Roghumi\Press\Crud\Services\RoleService\IRoleService;
use Roghumi\Press\Crud\Services\RoleService\RoleService;

class PressCrudServiceProvider extends ServiceProvider
{
    use MigrationPublishTrait;

    public function register()
    {
        $this->app->bind(IAccessService::class, AccessService::class);
        $this->app->bind(ICrudService::class, CrudService::class);
        $this->app->bind(IRoleService::class, RoleService::class);
        $this->app->bind(IDomainService::class, DomainService::class);
    }

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/press/crud.php', 'press.crud');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/crud.php');
        $this->loadTranslationsFrom(__DIR__ . '/../../lang/', 'press.crud');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/press/crud.php' => config_path('press/crud.php'),
            ], 'config');

            $this->publishMigrations(__DIR__ . '/../../database/migrations');
            if ($this->app->runningUnitTests()) {
                $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
            }

            $this->commands([
                MakeComposite::class,
            ]);
        }
    }
}
