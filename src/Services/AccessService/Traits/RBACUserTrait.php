<?php

namespace Roghumi\Press\Crud\Services\AccessService\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Resources\Group\Group;
use Roghumi\Press\Crud\Resources\Role\Role;
use Roghumi\Press\Crud\Services\AccessService\IAccessRole;

/**
 * A trait for building standard RBAC user
 * use this in your laravel applications User class.
 */
trait RBACUserTrait
{
    /**
     * @return int|string
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * User roles
     *
     * @return Collection<IAccessRole>
     */
    public function getRoles(): Collection
    {
        return $this->roles ?? new Collection([]);
    }

    /**
     * User groups
     *
     * @return Collection<IAccessGroup>
     */
    public function getGroups(): Collection
    {
        return $this->groups ?? new Collection([]);
    }

    /**
     * User top role
     */
    public function getTopRole(): ?IAccessRole
    {
        return $this->roles?->first();
    }

    /**
     * User permission names
     *
     * @return Collection<string>
     */
    public function getPermissionNames(): Collection
    {
        return $this->getTopRole()?->getPermissionNames() ?? new Collection([]);
    }

    /**
     * Get domains which user is a manager in
     *
     * @return Collection<IAccessDomain>
     */
    public function getManagingDomains(): Collection
    {
        return $this->domains ?? new Collection([]);
    }

    /**
     * The roles that belong to the user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
            ->withPivot(['z_order'])
            ->orderByPivot('z_order', 'desc');
    }

    /**
     * The domains that belong to the user
     */
    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'domain_user', 'user_id', 'domain_id');
    }

    /**
     * The groups that belong to the user
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id');
    }
}
