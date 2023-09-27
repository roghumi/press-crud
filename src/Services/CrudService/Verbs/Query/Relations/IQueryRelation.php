<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations;

use Closure;
use Illuminate\Validation\ValidationException;

interface IQueryRelation
{
    /**
     * Get a relation loading closure for eloquent model
     *
     * @param array $data
     *
     * @return Closure
     */
    public function createEagerLoadFunctionForRelation(array $data): Closure;

    /**
     * Validate relation loading data params
     *
     * @param array $data
     *
     * @throws ValidationException
     *
     * @return array
     */
    public function validateRelationRequestParams(array $data): array;
}
