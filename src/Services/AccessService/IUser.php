<?php

namespace Roghumi\Press\Crud\Services\AccessService;

use Illuminate\Support\Collection;

/**
 * RBAC User interface.
 * RBACUserTrait has the default implementation of
 * this interface.
 *
 */
interface IUser
{
    /**
     * User Id
     *
     * @return int|string
     */
    public function getId(): mixed;

    /**
     * User roles
     *
     * @return Collection<IAccessRole>
     */
    public function getRoles(): Collection;

    /**
     * User groups
     *
     * @return Collection<IAccessGroup>
     */
    public function getGroups(): Collection;

    /**
     * User top ranked role
     *
     * @return IAccessRole|null
     */
    public function getTopRole(): IAccessRole|null;

    /**
     * User permissions
     *
     * @return Collection<string>
     */
    public function getPermissionNames(): Collection;

    /**
     * User managing domains
     *
     * @return Collection<IAccessDomain>
     */
    public function getManagingDomains(): Collection;
}
