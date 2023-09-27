<?php

namespace Roghumi\Press\Crud\Tests\Mock\User;

use Illuminate\Database\Eloquent\Model;
use Roghumi\Press\Crud\Services\CrudService\ICrudResource;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Create\Create;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Query;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Update\Update;

class UserProvider implements ICrudResourceProvider
{
    /**
     *  get a unique name for this resource
     */
    public function getName(): string
    {
        return 'user';
    }

    /**
     * Fully qualified model class name for this resource
     */
    public function getModelClass(): string
    {
        return User::class;
    }

    /**
     * Retrieve object by its ID
     *
     * @param  int|string  $id
     */
    public function getObjectById($id): ?ICrudResource
    {
        return User::withTrashed()->find($id);
    }

    /**
     * Generate a new resource based on the data that can be stored later
     */
    public function generateModelFromData(array $data): ?Model
    {
        return new User($data);
    }

    /**
     * Verb composition map
     */
    public function getAvailableVerbAndCompositions(): array
    {
        return [
            Create::NAME => [
            ],
            Update::NAME => [
            ],
            Query::NAME => [
                QueryComposite::class,
            ],
        ];
    }
}
