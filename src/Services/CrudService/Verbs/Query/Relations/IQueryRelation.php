<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations;

use Closure;
use Illuminate\Validation\ValidationException;

interface IQueryRelation
{
    /**
     * Get a relation loading closure for eloquent model
     */
    public function createEagerLoadFunctionForRelation(array $data): Closure;

    /**
     * Validate relation loading data params
     *
     *
     * @throws ValidationException
     */
    public function validateRelationRequestParams(array $data): array;
}
