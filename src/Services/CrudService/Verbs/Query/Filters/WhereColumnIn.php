<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class WhereColumnIn implements IQueryFilter
{
    /**
     * Constructor
     *
     * @param  string  $name name of filter
     * @param  string  $column column name for this filter
     * @return WhereColumnIn
     */
    public function __construct(
        public string $name,
        public string $column
    ) {
    }

    /**
     * Static create
     *
     * @param  string  $name name of filter
     * @param  string  $column column name for this filter
     * @return WhereColumnIn
     */
    public static function create(string $name, string $column): IQueryFilter
    {
        return new WhereColumnIn($name, $column);
    }

    /**
     * Apply filter conditions for a value on query builder
     */
    public function applyFilter(Closure $filterFunction, Builder $query): Builder
    {
        $filterFunction($query);

        return $query;
    }

    /**
     * Generate a filter function for this specific filter with $data as required params
     *   which have been validated with validateFilterRequestParams
     */
    public function createFilterFunctionForRequestParams(array $data): Closure
    {
        $values = $data['values'];

        return function (Builder $query) use ($values) {
            $query->whereIn(
                $this->column,
                $values
            );
        };
    }

    /**
     * Validate request params for applying this filter
     *
     *
     * @throws ValidationException
     */
    public function validateFilterRequestParams(array $data): array
    {
        return Validator::validate($data, [
            'values' => 'required|array',
        ]);
    }
}
