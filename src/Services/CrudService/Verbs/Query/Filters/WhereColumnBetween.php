<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WhereColumnBetween implements IQueryFilter
{
    public function __construct(
        public string $name,
        public string $column,
    ) {
    }

    public static function create(string $name, string $column): IQueryFilter
    {
        return new WhereColumnBetween($name, $column);
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
        $startId = $data['start'];
        $endId = $data['end'];

        return function (Builder $query) use ($startId, $endId) {
            $query->whereBetween(
                $this->column,
                [
                    $startId,
                    $endId,
                ]
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
            'start' => 'required|numeric',
            'end' => 'required|numeric',
        ]);
    }
}
