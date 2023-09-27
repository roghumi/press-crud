<?php

namespace Roghumi\Press\Crud\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Roghumi\Press\Crud\Facades\AccessService;
use Roghumi\Press\Crud\Facades\RoleService;
use Roghumi\Press\Crud\Providers\PressCrudServiceProvider;
use Roghumi\Press\Crud\Resources\Role\Permission;
use Roghumi\Press\Crud\Resources\Role\Role;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;
use Roghumi\Press\Crud\Tests\Mock\User\User;
use Roghumi\Press\Crud\Tests\Mock\User\UserProvider;

class TestCase extends TestbenchTestCase
{
    use DatabaseMigrations;

    protected $adminUser;

    protected $adminRole;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('press.crud.user.class', User::class);
        Config::set('press.crud.user.provider', UserProvider::class);

        $this->runDatabaseMigrations();

        $this->adminUser = User::factory()->create();
        $this->adminRole = Role::factory()->create([
            'name' => 'super-admin',
        ]);
        RoleService::attachRoleToUser($this->adminRole->id, $this->adminUser->id);

        AccessService::updatePermissionsTable(config('press.crud.resources'));
        RoleService::attachPermissionsToRole(
            $this->adminRole->id,
            Permission::all()->pluck('name')->toArray()
        );

        $this->postSetup();
    }

    protected function postSetup()
    {
    }

    protected function getPackageProviders($app)
    {
        return [
            PressCrudServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
    }

    protected function getAvailableCrudVerbNames(): array
    {
        $availableVerbClasses = config('press.crud.verbs');
        $availableVerbNames = [];
        foreach ($availableVerbClasses as $verbClass) {
            /** @var ICrudVerb */
            $verb = new $verbClass();
            $availableVerbNames[] = $verb->getName();
        }

        return $availableVerbNames;
    }

    protected function getCrudVerbFromName(string $name): ICrudVerb
    {
        $availableVerbClasses = config('press.crud.verbs');
        foreach ($availableVerbClasses as $verbClass) {
            /** @var ICrudVerb */
            $verb = new $verbClass();
            if ($name === $verb->getName()) {
                return $verb;
            }
        }

        $this->fail("Crud verb with name $name is not found in verbs list.");
    }
}
