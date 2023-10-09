<?php

namespace Roghumi\Press\Crud\Services\RoleService;

use Illuminate\Support\Facades\DB;
use Roghumi\Press\Crud\Resources\Role\Permission;
use Roghumi\Press\Crud\Resources\Role\Role;

class RoleService implements IRoleService
{
    /**
     * Attach role to user roles list
     *
     *
     * @return void
     */
    public function attachRoleToUser(int|string $roleId, int|string $userId, int $zOrder = 0)
    {
        DB::table('role_user')->updateOrInsert([
            'user_id' => $userId,
            'role_id' => $roleId,
        ], [
            'z_order' => $zOrder,
        ]);
    }

    /**
     * Detach role from user
     *
     *
     * @return void
     */
    public function detachRoleFromUser(int|string $roleId, int|string $userId)
    {
        DB::table('role_user')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->delete();
    }

    /**
     * add permission to roles permissions list
     *
     * @param  string[]  $permissionsName
     * @return void
     */
    public function attachPermissionsToRole(int|string $roleId, array $permissionsName)
    {
        $availablePermissions = Permission::whereIn('name', $permissionsName)
            ->select('id')->pluck('id')->toArray();
        foreach ($availablePermissions as $permission) {
            DB::table('permission_role')->updateOrInsert([
                'role_id' => $roleId,
                'permission_id' => $permission,
            ]);
        }
    }

    /**
     * remove permission from roles permissions list
     *
     * @param  string[]  $permissionNames
     * @return void
     */
    public function detachPermissionsFromRole(int|string $roleId, array $permissionNames)
    {
        $availablePermissions = Permission::whereIn('name', $permissionNames)
            ->select('id')->pluck('id')->toArray();
        foreach ($availablePermissions as $permission) {
            DB::table('permission_role')
                ->where('role_id', $roleId)
                ->where('permission_id', $permission)
                ->delete();
        }
    }

    /**
     * sync user roles, array order becomes role orders
     *
     * @param  string[]  $roleIds
     * @return void
     */
    public function syncUserRoles(int|string $userId, array $roleIds)
    {
        $availableRoleIds = Role::select(['id'])->whereIn('id', $roleIds)->get()->pluck('id')->toArray();
        DB::transaction(function () use ($userId, $availableRoleIds) {
            DB::table('role_user')->where('user_id', $userId)->delete();
            foreach ($availableRoleIds as $indexer => $roleId) {
                DB::table('role_user')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'z_order' => $indexer,
                ]);
            }
        });
    }

    /**
     * sync user roles with name, array order becomes role orders
     *
     * @param  string[]  $roleNames
     * @return void
     */
    public function syncUserRolesWithName(int|string $userId, array $roleNames)
    {
        $availableRoleIds = Role::select(['id'])->whereIn('name', $roleNames)->get()->pluck('id')->toArray();
        DB::transaction(function () use ($userId, $availableRoleIds) {
            DB::table('role_user')->where('user_id', $userId)->delete();
            foreach ($availableRoleIds as $indexer => $roleId) {
                DB::table('role_user')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'z_order' => $indexer,
                ]);
            }
        });
    }

    /**
     * sync role permissions
     *
     * @param  string[]  $permissionNames
     * @return void
     */
    public function syncRolePermissions(int|string $roleId, array $permissionNames)
    {
        $availablePermissions = Permission::whereIn('name', $permissionNames)->select('id')->pluck('id')->toArray();
        DB::transaction(function () use ($roleId, $availablePermissions) {
            DB::table('permission_role')->where('role_id', $roleId)->delete();
            foreach ($availablePermissions as $permission) {
                DB::table('permission_role')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permission,
                ]);
            }
        });
    }
}
