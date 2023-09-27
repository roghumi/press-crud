<?php

namespace Roghumi\Press\Crud\Resources\Role;

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
 * Role Resource Provider
 * Used as the default definition for the `Role` crud resource.
 */
class RoleProvider implements ICrudResourceProvider
{
    /**
     *  get a unique name for this resource
     */
    public function getName(): string
    {
        return 'role';
    }

    /**
     * Fully qualified model class name for this resource
     */
    public function getModelClass(): string
    {
        return Role::class;
    }

    /**
     * Retrieve object by its ID
     *
     * @param  int|string  $id
     */
    public function getObjectById($id): ?ICrudResource
    {
        return Role::withTrashed()->find($id);
    }

    /**
     * Generate a new resource based on the data that can be stored later
     */
    public function generateModelFromData(array $data): ?Model
    {
        return new Role($data);
    }

    /**
     * Verb composition map
     */
    public function getAvailableVerbAndCompositions(): array
    {
        return [
            Create::NAME => [
                CreateComposite::class,
            ],
            Update::NAME => [
                UpdateComposite::class,
            ],
            Query::NAME => [
                QueryComposite::class,
            ],
            Export::NAME => [
                QueryComposite::class,
            ],
            Duplicate::NAME => [
                DuplicateComposite::class,
            ],
            Delete::NAME => [],
            Restore::NAME => [],
        ];
    }
}
