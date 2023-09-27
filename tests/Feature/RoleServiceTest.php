<?php

namespace Roghumi\Press\Crud\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Roghumi\Press\Crud\Facades\RoleService;
use Roghumi\Press\Crud\Resources\Role\Permission;
use Roghumi\Press\Crud\Resources\Role\Role;
use Roghumi\Press\Crud\Tests\Mock\User\User;
use Roghumi\Press\Crud\Tests\TestCase;

class RoleServiceTest extends TestCase
{
    /**
     * @group Service
     */
    public function test_user_role_attach()
    {
        User::factory(5)->create();
        Role::factory(5)->create();

        $this->assertDatabaseCount('users', 6); // 5 + admin user
        $this->assertDatabaseCount('roles', 6); // 5 + admin role

        // test user role attach
        $this->assertDatabaseCount('role_user', 1); // admin user-role
        RoleService::attachRoleToUser(2, 2); // new user as role 2
        $this->assertDatabaseCount('role_user', 2);
        RoleService::attachRoleToUser(3, 3); // do new user role
        $this->assertDatabaseCount('role_user', 3);
        RoleService::attachRoleToUser(4, 3); // do new user role
        $this->assertDatabaseCount('role_user', 4);
        RoleService::attachRoleToUser(3, 3); // do redundant
        $this->assertDatabaseCount('role_user', 4); // check redundant has no effect
        RoleService::detachRoleFromUser(3, 2); // do irrelevant
        $this->assertDatabaseCount('role_user', 4); // check redundant has no effect
        RoleService::detachRoleFromUser(2, 2); // detach user 2
        $this->assertDatabaseCount('role_user', 3); // check redundant has no effect
        RoleService::syncUserRoles(1, [55, 1, 2, 3, 4]);
        $this->assertDatabaseCount('role_user', 6); // check redundant has no effect

        // test role sync permissions
        $count = DB::table('permission_role')->count();
        $this->assertDatabaseCount('permission_role', $count);
        RoleService::syncRolePermissions(2, Permission::all()->pluck('name')->toArray());
        $this->assertDatabaseCount('permission_role', $count * 2);

        RoleService::detachPermissionsFromRole(2, ['crud.create.domain']);
        $this->assertDatabaseCount('permission_role', $count * 2 - 1);
        $this->assertCount($count - 1, Role::find(2)->getPermissionNames());
        $this->assertEquals(2, Role::find(3)->accounts()->count());

        // test user retrieve permissions
        RoleService::attachRoleToUser(4, 1, 10);

        $this->assertEquals(4, User::find(1)->getRoles()->count());
        $this->assertEquals(4, User::find(1)->getTopRole()->id);
        $this->assertEquals(0, User::find(1)->getPermissionNames()->count());
        RoleService::attachPermissionsToRole(4, Permission::all()->pluck('name')->toArray());
        $this->assertEquals($count, User::find(1)->getPermissionNames()->count());

        RoleService::syncUserRoles(2, [1, 2, 3]);
        $this->assertCount(0, array_diff([1, 2, 3], User::find(2)->getRoles()->pluck('id')->toArray()));

        RoleService::syncUserRolesWithName(2, Role::whereIn('id', [4, 5])->pluck('name')->toArray());
        $this->assertCount(0, array_diff([4, 5], User::find(2)->getRoles()->pluck('id')->toArray()));
    }
}
