<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations;

use Closure;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class QueryHasManyRelation implements IQueryRelation
{
    use QueryRelationTrait;

    public static function create(string $name, string $providerClass, Request $request, ...$args): IQueryRelation
    {
        return new QueryHasManyRelation($request, $name, $providerClass, ...$args);
    }

    /**
     * Get a relation loading closure for eloquent model
     */
    public function createEagerLoadFunctionForRelation(array $data): Closure
    {
        return function (HasMany $relation) use ($data) {
            if (isset($data['columns']) && ! is_null($data['columns'])) {
                $relation->select($data['columns']);
            }
        };
    }
}
