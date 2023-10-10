<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

/**
 * Filter a resource based on a columns value.
 * You can pass an operator to filter for values with
 * equal, less, greater value.
 */
class WhereColumnEquals implements IQueryFilter
{
    /**
     * Constructor
     *
     * @param  string  $name name of filter
     * @param  string  $column column name for this filter
     * @return WhereColumnEquals
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
     * @return WhereColumnEquals
     */
    public static function create(string $name, string $column): IQueryFilter
    {
        return new WhereColumnEquals($name, $column);
    }

    /**
     * Apply filter conditions for a $filterFunction created by this filter on the $query
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
        $operator = $data['operator'];
        $value = $data['value'];

        return function (Builder $query) use ($operator, $value) {
            $query->where(
                $this->column,
                $operator,
                $value
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
            'operator' => 'required|string|in:'.implode(',', [
                '=', '>', '>=', '<=', '<', '<>',
            ]),
            'value' => 'required|',
        ]);
    }
}
