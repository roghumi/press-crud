<?php

namespace Roghumi\Press\Crud\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Roghumi\Press\Crud\Services\DomainService\IDomainService;

/**
 * Press DomainService facade
 *
 * @method static void addUserToDomain(int|string $userId, int|string $domainId)
 * @method static void removeUserFromDomain(int|string $userId, int|string $domainId)
 * @method static void addDomainAsChild(int|string $parentId, int|string $childId)
 * @method static void addAllDomainAsChild(int|string $parentId, array $childIds)
 * @method static void removeDomainFromParent(int|string $domainId)
 * @method static Collection getAncestorDomainIds(int|string $domainId)
 * @method static Collection getChildDomainIds(int|string $domainId)
 * @method static int|string getRootDomainId(int|string $domainId)
 * @method static int|string|null getFirstParentId(int|string $domainId)
 */
class DomainService extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return IDomainService::class;
    }
}
