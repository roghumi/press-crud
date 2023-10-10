<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

/**
 * Interface for implementing custom filters for crud resource querying.
 * Crud Query verb uses this filters to validate and apply query filters.
 */
interface IQueryFilter
{
    /**
     * Apply filter conditions for a $filterFunction created by this filter on the $query
     */
    public function applyFilter(Closure $filterFunction, Builder $query): Builder;

    /**
     * Generate a filter function for this specific filter with $data as required params
     *   which have been validated with validateFilterRequestParams
     */
    public function createFilterFunctionForRequestParams(array $data): Closure;

    /**
     * Validate request params for applying this filter
     *
     *
     * @throws ValidationException
     */
    public function validateFilterRequestParams(array $data): array;
}
