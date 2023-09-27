<?php

namespace Roghumi\Press\Crud\Services\RoleService\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Roghumi\Press\Crud\Resources\Role\Permission;
use Roghumi\Press\Crud\Services\RoleService\IRole;

/**
 *  A trait for building standard RBAC role
 *      use this in your laravel applications custom Role class if needed
 */
trait RBACRoleTrait
{
    /**
     * Get unique id for this role
     *
     * @return int|string
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * Get name for this role
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get permissions associated with this role
     *
     * @return Collection<string>
     */
    public function getPermissionNames(): Collection
    {
        return $this->permissions->pluck('name');
    }

    /**
     * is this role a super admin role
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return ($this->options & IRole::OPTIONS_SUPER_ADMIN_ROLE) != 0;
    }

    /**
     * is this role a domain admin role
     *
     * @return bool
     */
    public function isDomainAdmin(): bool
    {
        return ($this->options & IRole::OPTIONS_DOMAIN_ADMIN_ROLE) != 0;
    }

    /**
     * is this role a group admin role
     *
     * @return bool
     */
    public function isGroupAdmin(): bool
    {
        return ($this->options & IRole::OPTIONS_GROUP_ADMIN_ROLE) != 0;
    }

    /**
     * Is this role a none admin role.
     * None admin roles only can see what they have
     * authored only.
     *
     * @return bool
     */
    public function isNonAdmin(): bool
    {
        return ($this->options &
            (IRole::OPTIONS_GROUP_ADMIN_ROLE | IRole::OPTIONS_DOMAIN_ADMIN_ROLE | IRole::OPTIONS_SUPER_ADMIN_ROLE)
        ) == 0;
    }

    /**
     * Get maximum clearance for user options level.
     * This is a tool to prevent users with lower clearance
     * creating roles with higher clearance values.
     *
     * @return int
     */
    public function getOptionsLevel(): int
    {
        return $this->options;
    }

    /**
     * Author of this role
     *
     * @crud-relation
     *
     * @provider UserAccountProvider
     *
     * @return HasOne
     */
    public function author()
    {
        return $this->hasOne(config('press.crud.user.class'), 'author_id');
    }

    /**
     * Accounts with this role
     *
     * @crud-relation
     *
     * @provider UserAccountProvider
     *
     * @return BelongsToMany
     */
    public function accounts()
    {
        return $this
            ->belongsToMany(config('press.crud.user.class'), 'role_user', 'role_id', 'user_id')
            ->withPivot(['z_order'])
            ->orderByPivot('z_order', 'desc');
    }

    /**
     * Role permissions
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }
}
