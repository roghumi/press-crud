<?php

namespace Roghumi\Press\Crud\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Roghumi\Press\Crud\Facades\AccessService;
use Roghumi\Press\Crud\Facades\RoleService;
use Roghumi\Press\Crud\Resources\Domain\DomainProvider;
use Roghumi\Press\Crud\Resources\Role\Permission;
use Roghumi\Press\Crud\Resources\Role\Role;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Create\Create;
use Roghumi\Press\Crud\Tests\Mock\User\User;
use Roghumi\Press\Crud\Tests\TestCase;

class AccessServiceTest extends TestCase
{
    /**
     * @group Service
     */
    public function test_access_service_authorizing()
    {

        User::factory(5)->create();
        Role::factory(3)->create();

        RoleService::attachRoleToUser(2, 2);
        RoleService::attachRoleToUser(3, 3);
        RoleService::attachRoleToUser(4, 4);

        RoleService::attachPermissionsToRole(2, Permission::all()->pluck('name')->toArray());
        RoleService::attachPermissionsToRole(3, Permission::all()->pluck('name')->toArray());
        RoleService::detachPermissionsFromRole(3, ['crud.create.domain', 'crud.duplicate.domain']);

        $this->assertTrue(AccessService::hasAccessToVerb(User::find(2), 'domain', 'create'));
        $this->assertFalse(AccessService::hasAccessToVerb(User::find(3), 'domain', 'create'));
        $this->assertFalse(AccessService::hasAccessToVerb(User::find(3), 'domain', 'duplicate'));
        $this->assertTrue(AccessService::hasAccessToVerb(User::find(3), 'domain', 'update'));

        $this->assertFalse(AccessService::hasAccessToVerb(User::find(4), 'domain', 'create'));
        $this->assertFalse(AccessService::hasAccessToVerb(User::find(4), 'domain', 'duplicate'));
        RoleService::attachPermissionsToRole(4, ['crud.create.domain', 'crud.duplicate.domain']);
        $this->assertTrue(AccessService::hasAccessToVerb(User::find(4), 'domain', 'create'));
        $this->assertTrue(AccessService::hasAccessToVerb(User::find(4), 'domain', 'duplicate'));
    }

    public function test_access_service_routes()
    {
        $provider = new DomainProvider();
        $verb = new Create();
        $metadata = AccessService::getCrudRouteMetadata($verb, $provider);
        $route = Route::match('POST', '/sample-route', $metadata)->name('crud.verb.sample');

        $this->assertArrayHasKey('provider_class', $metadata);
        $this->assertArrayHasKey('verb_class', $metadata);
        $this->assertTrue(AccessService::isValidCrudRoute($route));

        $this->assertEquals(AccessService::getProviderClassFromRoute($route), DomainProvider::class);
        $this->assertEquals(AccessService::getVerbClassFromRoute($route), Create::class);
        $this->assertTrue(AccessService::getProviderFromRoute($route) instanceof DomainProvider);
        $this->assertTrue(AccessService::getVerbFromRoute($route) instanceof Create);

        $invalidRoute = Route::match('POST', '/sample');
        $this->assertFalse(AccessService::isValidCrudRoute($invalidRoute));
        $this->assertNull(AccessService::getProviderFromRoute($invalidRoute));
        $this->assertNull(AccessService::getVerbFromRoute($invalidRoute));
    }
}
