<?php

namespace Roghumi\Press\Crud\Services\RoleService;

/**
 * Role services interface.
 * Manage user roles and role permissions.
 */
interface IRoleService
{
    /**
     * Attach role to user roles list
     *
     * @param int|string $roleId
     * @param int|string $userId
     * @param int $zOrder
     *
     * @return void
     */
    public function attachRoleToUser(int|string $roleId, int|string $userId, int $zOrder = 0);

    /**
     * Detach role from user
     *
     * @param int|string $roleId
     * @param int|string $userId
     *
     * @return void
     */
    public function detachRoleFromUser(int|string $roleId, int|string $userId);

    /**
     * add permission to roles permissions list
     *
     * @param int|string $roleId
     * @param  string[]  $permissionsName
     *
     * @return void
     */
    public function attachPermissionsToRole(int|string $roleId, array $permissionsName);

    /**
     * remove permission from roles permissions list
     *
     * @param int|string $roleId
     * @param  string[]  $permissionNames
     *
     * @return void
     */
    public function detachPermissionsFromRole(int|string $roleId, array $permissionNames);

    /**
     * sync user roles, array order becomes role orders
     *
     * @param int|string $userId
     * @param  string[]  $roleIds
     *
     * @return void
     */
    public function syncUserRoles(int|string $userId, array $roleIds);

    /**
     * sync user roles with name, array order becomes role orders
     *
     * @param int|string $userId
     * @param  string[]  $roleNames
     *
     * @return void
     */
    public function syncUserRolesWithName(int|string $userId, array $roleNames);

    /**
     * sync role permissions
     *
     * @param int|string $roleId
     * @param string[] $permissionNames
     *
     * @return void
     */
    public function syncRolePermissions(int|string $roleId, array $permissionNames);
}
