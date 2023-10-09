<?php

namespace Roghumi\Press\Crud\Services\DomainService;

use Illuminate\Support\Collection;

/**
 * Interface to interact with an object in database representing a "Domain"
 */
interface IDomain
{
    /**
     * Get unique id for this domain
     *
     * @return int|string
     */
    public function getId(): mixed;

    /**
     * Get name for this domain
     */
    public function getDomainName(): string;

    /**
     * Get users associated with this domain
     *
     * @return Collection<IUser>
     */
    public function getUsers(): Collection;

    /**
     * Get domain ancestors as a collection
     *
     * @return Collection<IDomain>
     */
    public function getAncestors(): Collection;

    /**
     * Get domain descendants as a collection
     *
     * @return Collection<IDomain>
     */
    public function getDescendants(): Collection;
}
