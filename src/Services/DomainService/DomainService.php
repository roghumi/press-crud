<?php

namespace Roghumi\Press\Crud\Services\DomainService;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Roghumi\Press\Crud\Helpers\HierarchyHelpers;

/**
 * Domain services.
 * Manage domain hierarchy, and user-domain connection.
 */
class DomainService implements IDomainService
{
    /**
     * Add a user to a domain
     *
     *
     * @return void
     */
    public function addUserToDomain(int|string $userId, int|string $domainId)
    {
        DB::table('domain_user')->updateOrInsert([
            'user_id' => $userId,
            'domain_id' => $domainId,
        ]);
    }

    /**
     * Remove a user from a domain
     *
     *
     * @return void
     */
    public function removeUserFromDomain(int|string $userId, int|string $domainId)
    {
        DB::table('domain_user')->where('user_id', $userId)->where('domain_id', $domainId)->delete();
    }

    /**
     * Connect domains as parent child
     *
     *
     * @return void
     */
    public function addDomainAsChild(int|string $parentId, int|string $childId)
    {
        HierarchyHelpers::addHierarchyAsChild($parentId, $childId, 'domain_hierarchy');
    }

    /**
     * Connect multiple records to a parent as children
     *
     *
     * @return void
     */
    public function addAllDomainAsChild(int|string $parentId, array $childIds)
    {
        HierarchyHelpers::addHierarchiesAsChild($parentId, $childIds, 'domain_hierarchy');
    }

    /**
     * Remove domain from parent, and make it a root domain
     *
     *
     * @return void
     */
    public function removeDomainFromParent(int|string $domainId)
    {
        HierarchyHelpers::removeHierarchyFromParent($domainId, 'domain_hierarchy');
    }

    /**
     * Get a collection of domain ancestors
     *
     *
     * @return Closure
     */
    public function getAncestorDomainIds(int|string $domainId): Collection
    {
        return HierarchyHelpers::getAncestorHierarchyIds($domainId, 'domain_hierarchy');
    }

    /**
     * Get a list of a domains descendants
     */
    public function getChildDomainIds(int|string $domainId): Collection
    {
        return HierarchyHelpers::getChildHierarchyIds($domainId, 'domain_hierarchy');
    }

    /**
     * Get root domain Id for this domain Id
     */
    public function getRootDomainId(int|string $domainId): int|string
    {
        return HierarchyHelpers::getRootHierarchyId($domainId, 'domain_hierarchy');
    }

    /**
     * Get parent of this domain if exists
     *
     *
     * @return int|string
     */
    public function getFirstParentId(int|string $domainId): int|string|null
    {
        return HierarchyHelpers::getFirstParentHierarchyId($domainId, 'domain_hierarchy');
    }
}
