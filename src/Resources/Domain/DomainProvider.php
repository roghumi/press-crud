<?php

namespace Roghumi\Press\Crud\Resources\Domain;

use Illuminate\Database\Eloquent\Model;
use Roghumi\Press\Crud\Services\CrudService\ICrudResource;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Create\Create;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Delete\Delete;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Duplicate\Duplicate;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Export\Export;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Query;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Restore\Restore;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Update\Update;

/**
 * Domain Resource Provider
 * Used as the default definition for the `Domain` crud resource.
 */
class DomainProvider implements ICrudResourceProvider
{
    /**
     * get a unique name for this resource
     *
     * @return string
     */
    public function getName(): string
    {
        return 'domain';
    }

    /**
     * Fully qualified model class name for this resource
     *
     * @return string
     */
    public function getModelClass(): string
    {
        return Domain::class;
    }

    /**
     * Retrieve object by its ID
     *
     * @param  int|string  $id
     *
     * @return ICrudResource|null
     */
    public function getObjectById($id): ?ICrudResource
    {
        return Domain::withTrashed()->find($id);
    }

    /**
     * Generate a new resource based on the data that can be stored later
     *
     * @param array $data
     *
     * @return Model|null
     */
    public function generateModelFromData(array $data): ?Model
    {
        return new Domain($data);
    }

    /**
     * Verb composition map for Domain
     *
     * @return array
     */
    public function getAvailableVerbAndCompositions(): array
    {
        return [
            Create::NAME => [
                new CreateComposite(),
            ],
            Update::NAME => [
                new UpdateComposite(),
            ],
            Query::NAME => [
                new QueryComposite(),
            ],
            Export::NAME => [
                new QueryComposite(),
            ],
            Duplicate::NAME => [
                new DuplicateComposite(),
            ],
            Delete::NAME => [],
            Restore::NAME => [],
        ];
    }
}
