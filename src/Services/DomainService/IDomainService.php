<?php

namespace Roghumi\Press\Crud\Services\DomainService;

use Illuminate\Support\Collection;

/**
 * Domain services interface.
 * Manage domain hierarchy, and user-domain relation.
 */
interface IDomainService
{
    /**
     * Add a user to a domain
     *
     * @param int|string $userId
     * @param int|string $domainId
     *
     * @return void
     */
    public function addUserToDomain(int|string $userId, int|string $domainId);

    /**
     * Remove a user from a domain
     *
     * @param int|string $userId
     * @param int|string $domainId
     *
     * @return void
     */
    public function removeUserFromDomain(int|string $userId, int|string $domainId);

    /**
     * Connect domains as parent child
     *
     * @param int|string $parentId
     * @param int|string $childId
     *
     * @return void
     */
    public function addDomainAsChild(int|string $parentId, int|string $childId);

    /**
     * Connect multiple records to a parent as children
     *
     * @param int|string $parentId
     * @param array $childIds
     *
     * @return void
     */
    public function addAllDomainAsChild(int|string $parentId, array $childIds);

    /**
     * Remove domain from parent, and make it a root domain
     *
     * @param int|string $domainId
     *
     * @return void
     */
    public function removeDomainFromParent(int|string $domainId);

    /**
     * Get a collection of domain ancestors
     *
     * @param int|string $domainId
     *
     * @return Collection
     */
    public function getAncestorDomainIds(int|string $domainId): Collection;

    /**
     * Get a list of a domains descendants
     *
     * @param int|string $domainId
     *
     * @return Collection
     */
    public function getChildDomainIds(int|string $domainId): Collection;

    /**
     * Get root domain Id for this domain Id
     *
     * @param int|string $domainId
     *
     * @return int|string
     */
    public function getRootDomainId(int|string $domainId): int|string;


    /**
     * Get parent of this domain if exists
     *
     * @param int|string $domainId
     *
     * @return int|string
     */
    public function getFirstParentId(int|string $domainId): int|string|null;
}
