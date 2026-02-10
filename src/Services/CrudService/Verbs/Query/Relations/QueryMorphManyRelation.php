<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations;

use Closure;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;

class QueryMorphManyRelation implements IQueryRelation
{
    use QueryRelationTrait;

    /**
     * static factory method
     *
     * @param  mixed  ...$args
     */
    public static function create(string $name, string $providerClass, Request $request, ...$args): IQueryRelation
    {
        return new QueryMorphManyRelation($request, $name, $providerClass, ...$args);
    }

    /**
     * Get a relation loading closure for eloquent model
     */
    public function createEagerLoadFunctionForRelation(array $data): Closure
    {
        return function (MorphMany $relation) use ($data) {
            if (isset($data['columns']) && ! is_null($data['columns'])) {
                $relation->select($data['columns']);
            }
        };
    }
}
