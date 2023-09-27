<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Traits;

use Illuminate\Http\Request;

trait QueryCompositeTrait
{
    /**
     * Eloquent model creation/manipulation is not used in Query
     *
     * @param Request $request incoming request.
     * @param array $compositeData composition data chained to this composite.
     * @param mixed ...$args incoming route args.
     *
     * @return array
     */
    public function getSanitized(Request $request, array $compositeData, ...$args): array
    {
        return array_merge($compositeData, [
        ]);
    }

    /**
     * Rules for query request on this resource
     * these are general rules. Query verb will more
     * customize them based on the resource provider and
     * available columns,relations and filters
     *
     * @param Request $request incoming request.
     * @param array $compositeRules composition data chained to this composite.
     * @param mixed ...$args incoming route args.
     *
     * @return array
     */
    public function getRules(Request $request, array $compositeRules, ...$args): array
    {
        return array_merge($compositeRules, [
            'includeTrash' => 'nullable|bool',
            'columns' => ['nullable', 'array'],
            'filters' => ['nullable', 'array'],
            'relations' => ['nullable', 'array'],
            'sortBy' => ['nullable', 'array'],
            'perPage' => 'nullable|numeric|min:1|max:' . config('press.crud.query.maxPerPage'),
            'page' => 'nullable|numeric|min:0',
        ]);
    }
}
