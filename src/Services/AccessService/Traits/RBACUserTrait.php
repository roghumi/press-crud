<?php

namespace Roghumi\Press\Crud\Services\AccessService\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Resources\Group\Group;
use Roghumi\Press\Crud\Resources\Role\Role;
use Roghumi\Press\Crud\Services\AccessService\IAccessRole;

/**
 *  A trait for building standard RBAC user
 *      use this in your laravel applications User class
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
     * Undocumented function
     *
     * @return Collection<IAccessRole>
     */
    public function getRoles(): Collection
    {
        return $this->roles ?? new Collection([]);
    }

    /**
     * Undocumented function
     *
     * @return Collection<IAccessGroup>
     */
    public function getGroups(): Collection
    {
        return $this->groups ?? new Collection([]);
    }

    /**
     * Undocumented function
     *
     * @return IAccessRole|null
     */
    public function getTopRole(): IAccessRole|null
    {
        return $this->roles?->first();
    }

    /**
     * Undocumented function
     *
     * @return Collection<string>
     */
    public function getPermissionNames(): Collection
    {
        return $this->getTopRole()?->getPermissionNames() ?? new Collection([]);
    }

    /**
     * Undocumented function
     *
     * @return Collection<IAccessDomain>
     */
    public function getManagingDomains(): Collection
    {
        return $this->domains ?? new Collection([]);
    }

    /**
     * The roles that belong to the user
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
            ->withPivot(['z_order'])
            ->orderByPivot('z_order', 'desc');
    }

    /**
     * The domains that belong to the user
     *
     * @return BelongsToMany
     */
    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'domain_user', 'user_id', 'domain_id');
    }

    /**
     * The groups that belong to the user
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id');
    }
}
