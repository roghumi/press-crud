<?php

namespace Roghumi\Press\Crud\Facades;

use Illuminate\Support\Facades\Facade;
use Roghumi\Press\Crud\Services\RoleService\IRoleService;

/**
 * Press RoleService facade
 *
 * @method static void detachRoleFromUser(int|string $roleId, int|string $userId)
 * @method static void attachRoleToUser(int|string $roleId, int|string $userId, int $zOrder)
 * @method static void detachPermissionsFromRole(int|string $roleId, array $permissionNames)
 * @method static void attachPermissionsToRole(int|string $roleId, array $permissionNames)
 * @method static void syncUserRoles(int|string $userId, array $rolesIds)
 * @method static void syncUserRolesWithName(int|string $user, array $rolesNames)
 * @method static void syncRolePermissions(int|string $roleId, array $permissionNames)
 */
class RoleService extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return IRoleService::class;
    }
}
