<?php

namespace Roghumi\Press\Crud\Resources\Group;

use Illuminate\Database\Eloquent\Model;
use Roghumi\Press\Crud\Services\CrudService\ICrudResource;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Create\Create;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Update\Update;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Query;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Clone\CloneVerb;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Export\Export;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Delete\Delete;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Restore\Restore;

/**
 * Group Resource Provider
 * Used as the default definition for the `Domain` crud resource.
 */
class GroupProvider implements ICrudResourceProvider
{
    /**
     * get a unique name for this resource
     */
    public function getName(): string
    {
        return 'group';
    }

    /**
     * Fully qualified model class name for this resource
     */
    public function getModelClass(): string
    {
        return Group::class;
    }

    /**
     * Retrieve object by its ID
     *
     * @param  int|string  $id
     */
    public function getObjectById($id): ?ICrudResource
    {
        return Group::withTrashed()->find($id);
    }

    /**
     * Generate a new resource based on the data that can be stored
     */
    public function generateModelFromData(array $data): ?Model
    {
        return new Group($data);
    }

    /**
     * Get a map of available verbs (ICrudVerb interface unique names)
     *  to list of that verb compositions for this resource
     */
    public function getAvailableVerbAndCompositions(): array
    {
        return [
            Create::class => [CreateComposite::class],
Update::class => [UpdateComposite::class],
Query::class => [QueryComposite::class],
CloneVerb::class => [CloneComposite::class],
Export::class => [],
Delete::class => [],
Restore::class => []
        ];
    }
}
