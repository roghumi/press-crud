<?php

namespace Roghumi\Press\Crud\Tests\Mock\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Columns\IQueryColumn;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Columns\QueryColumn;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\IQueryFilter;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnBetween;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnEquals;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\IQueryVerbComposite;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\IQueryRelation;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Traits\QueryCompositeTrait;

/**
 * Mocked user query composite class
 */
class QueryComposite implements IQueryVerbComposite
{
    // use standard getSanitized, getRules for query compositions
    use QueryCompositeTrait;

    /**
     * Undocumented function
     *
     * @return array<IQueryColumn>
     */
    public function getColumns(Request $request, array $compositeColumns, ...$args): array
    {
        return [
            QueryColumn::create('id', true),
            QueryColumn::create('email', true),
            QueryColumn::create('created_at', true),
            QueryColumn::create('updated_at', true),
            QueryColumn::create('deleted_at', true),
        ];
    }

    /**
     * List of valid filters for this resource
     *
     * @return array<IQueryFilter>
     */
    public function getFilters(Request $request, array $compositeFilters, ...$args): array
    {
        return [
            WhereColumnEquals::create('id.equals', 'id'),
            WhereColumnBetween::create('id.between', 'id'),
        ];
    }

    /**
     * List of valid relations for this resource
     *
     * @return array<IQueryRelation>
     */
    public function getRelations(Request $request, array $compositeRelations, ...$args): array
    {
        return [
        ];
    }

    /**
     * List of valid aggregate-able columns
     */
    public function getAggregateOptions(Request $request, array $compositeAggregations, ...$args): array
    {
        return [
        ];
    }

    /**
     * On before query executed. Here is the best place for filtering results based on
     *  groups and domains and roles.
     */
    public function onBeforeQuery(Request $request, Builder $query, ...$args): void
    {
    }

    /**
     * After query is resolved. You can manipulated queried data before sending them as response
     */
    public function onAfterQuery(Request $request, Builder $query, LengthAwarePaginator $paginatedResult, ...$args): void
    {
    }
}
