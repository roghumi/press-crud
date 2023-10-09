<?php

namespace Roghumi\Press\Crud\Services\DomainService\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Resources\Domain\DomainHierarchy;

/**
 * A trait for building standard RBAC domain
 *  its a basic implementation for IAccessDomain
 */
trait RBACDomainTrait
{
    /**
     * Get unique id for this domain
     *
     * @return int|string
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * Get name for this domain
     */
    public function getDomainName(): string
    {
        return $this->name;
    }

    /**
     * Get users associated with this domain
     *
     * @return Collection<IUser>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Get domain ancestors as a collection
     *
     * @return Collection<IDomain>
     */
    public function getAncestors(): Collection
    {
        return $this->ancestors;
    }

    /**
     * Get domain descendants as a collection
     *
     * @return Collection<IDomain>
     */
    public function getDescendants(): Collection
    {
        return $this->descendants;
    }

    /**
     * Domain author
     *
     * @crud-relation
     *
     * @provider UserAccountProvider
     *
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(config('press.crud.user.class'), 'author_id');
    }

    /**
     * root domain for this subdomain
     *
     * @crud-relation
     *
     * @provider UserProvider
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('press.crud.user.class'));
    }

    /**
     * domain ancestors
     *
     * @crud-relation
     *
     * @provider DomainProvider
     *
     * @return HasManyThrough
     */
    public function ancestors()
    {
        return $this->hasManyThrough(
            Domain::class,
            DomainHierarchy::class,
            'child_id',
            'id',
            'id',
            'parent_id',
        );
    }

    /**
     * domain descendants
     *
     * @crud-relation
     *
     * @provider DomainProvider
     *
     * @return HasManyThrough
     */
    public function descendants()
    {
        return $this->hasManyThrough(
            Domain::class,
            DomainHierarchy::class,
            'parent_id',
            'id',
            'id',
            'child_id',
        );
    }
}
