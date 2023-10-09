<?php

namespace Roghumi\Press\Crud\Services\AccessService;

use Roghumi\Press\Crud\Services\RoleService\IRole;

/**
 * RBAC Role interface.
 * RBACRoleTrait has the default implementation
 * of this interface, with an 32bit flag integer, it
 * can store up to 32 boolean options for a role.
 */
interface IAccessRole extends IRole
{
    /**
     * Is this role a super admin role.
     * Super admin roles can access resources in
     * any domain or group.
     */
    public function isSuperAdmin(): bool;

    /**
     * Is this role a domain admin role.
     * Domain admin roles can access resources in their
     * administrative domains only.
     */
    public function isDomainAdmin(): bool;

    /**
     * Is this role a group admin role.
     * Group admin roles can access resources in their
     * administrative groups only.
     */
    public function isGroupAdmin(): bool;

    /**
     * Is this role a none admin role.
     * None admin roles only can see what they have
     * authored only.
     */
    public function isNonAdmin(): bool;

    /**
     * Get maximum clearance for user options level.
     * This is a tool to prevent users with lower clearance
     * creating roles with higher clearance values.
     */
    public function getOptionsLevel(): int;
}
