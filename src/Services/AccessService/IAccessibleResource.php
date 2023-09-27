<?php

namespace Roghumi\Press\Crud\Services\AccessService;

use Illuminate\Support\Collection;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\DomainService\IDomain;

/**
 * An interface for resources that want to be controlled
 * with RBAC. Implementing this interface for a resource
 * makes it responsive to domain and group based access controls.
 * Common use cases are implemented with traits:
 *
 */
interface IAccessibleResource
{
    /**
     * is this resource segmented by group?
     *
     * @return bool
     */
    public function isSegmentedByGroups(): bool;

    /**
     * is this resource segmented by domain?
     *
     * @return bool
     */
    public function isSegmentedByDomains(): bool;

    /**
     * Get the groups this resource belongs to
     *
     * @return Collection<IGroup>
     */
    public function getGroups(): Collection;

    /**
     * Get the domains this resource belongs to
     *
     * @return Collection<IDomain>
     */
    public function getDomains(): Collection;

    /**
     * get resource provider for this resource
     *
     * @return ICrudResourceProvider
     */
    public function getCrudProvider(): ICrudResourceProvider;
}
