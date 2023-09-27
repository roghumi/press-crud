<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations;

use Closure;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class QueryBelongsToRelation implements IQueryRelation
{
    use QueryRelationTrait;

    /**
     * Create new instance of this query relation.
     *
     * @param string $name
     * @param string $providerClass
     * @param Request $request
     * @param mixed ...$args
     *
     * @return IQueryRelation
     */
    public static function create(string $name, string $providerClass, Request $request, ...$args): IQueryRelation
    {
        return new QueryBelongsToRelation($request, $name, $providerClass, ...$args);
    }

    /**
     * Get a relation loading closure for eloquent model
     *
     * @param array $data
     *
     * @return Closure
     */
    public function createEagerLoadFunctionForRelation(array $data): Closure
    {
        return function (BelongsTo $relation) use ($data) {
            if (isset($data['columns']) && ! is_null($data['columns'])) {
                $relation->select($data['columns']);
            }
        };
    }
}
