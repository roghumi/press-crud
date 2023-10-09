<?php

namespace Roghumi\Press\Crud\Services\RoleService;

use Illuminate\Support\Collection;

/**
 * Interface to interact with an object in database representing a "Role"
 */
interface IRole
{
    // Role is not an admin (Can see resources that authored himself only)
    public const OPTIONS_NOT_ADMIN_ROLE = 0;

    // Role is a Super Admin (can see resources from every domain and group)
    public const OPTIONS_SUPER_ADMIN_ROLE = PHP_INT_MAX;

    // Role is a Domain Admin (Can see resource in his administrative domains only)
    public const OPTIONS_DOMAIN_ADMIN_ROLE = 1024;

    // Role is a Group Admin (Can see resource in his administrative groups only)
    public const OPTIONS_GROUP_ADMIN_ROLE = 512;

    /**
     * Get unique id for this role
     *
     * @return int|string
     */
    public function getId(): mixed;

    /**
     * Get name for this role
     */
    public function getName(): string;

    /**
     * Get permissions associated with this role
     *
     * @return Collection<string>
     */
    public function getPermissionNames(): Collection;
}
