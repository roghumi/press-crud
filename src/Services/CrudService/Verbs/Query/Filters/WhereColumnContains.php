<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class WhereColumnContains implements IQueryFilter
{
    public const Operator2Value = [
        'contains' => function ($value) {
            return '%'.$value.'%';
        },
        'startsWith' => function ($value) {
            return $value.'%';
        },
        'endsWith' => function ($value) {
            return '%'.$value;
        },
    ];
    
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
        return new WhereColumnContains($name, $column);
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
        $values = $data['values'];
        $not = $data['not'] ?? false;
        $operator = $data['operator'] ?? 'contains';
        $caseSensitive = $data['caseSensitive'] ?? true;
        // @todo: implement case sensitive

        return function (Builder $query) use ($values, $not, $operator, $caseSensitive) {
            if ($not) {
                $query->whereNot($this->column, 'LIKE', self::Operator2Value[$operator]($values));
            } else {
                $query->where($this->column, 'LIKE', self::Operator2Value[$operator]($values));
            }
        };
    }

    /**
     * Validate request params for applying this filter
     *
     * @throws ValidationException
     */
    public function validateFilterRequestParams(array $data): array
    {
        return Validator::validate($data, [
            'not' => 'nullable|boolean',
            'value' => 'required|array',
            'caseSensitive' => 'nullable|bool',
            'operator' => 'nullable|in:startsWith,endsWith,contains',
        ]);
    }
}
