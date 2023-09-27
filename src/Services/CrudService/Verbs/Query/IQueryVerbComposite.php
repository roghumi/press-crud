<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerbComposite;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Columns\IQueryColumn;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\IQueryFilter;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\IQueryRelation;

/**
 * Query verb composite interface. Used by Query verb to create a query based on
 * an incoming request. Implement this class for each resource you want to be able
 * to query.
 */
interface IQueryVerbComposite extends ICrudVerbComposite
{
    /**
     * Return a list of valid columns for this resource.
     * Other composites may influence the result when final
     * verb execution is reached.
     *
     * @param Request $request incoming request
     * @param array $compositeColumns
     * @param mixed ...$args
     *
     * @return array<IQueryColumn>
     */
    public function getColumns(Request $request, array $compositeColumns, ...$args): array;

    /**
     * Return a list of valid filters for this resource.
     * Other composites may influence the result when final
     * verb execution is reached.
     *
     * @param Request $request incoming request
     * @param array $compositeFilters
     * @param mixed ...$args
     *
     * @return array<IQueryFilter>
     */
    public function getFilters(Request $request, array $compositeFilters, ...$args): array;

    /**
     * Return a list of valid relations for this resource.
     * Other composites may influence the result when final
     * verb execution is reached.
     *
     * @param Request $request incoming request
     * @param array $compositeRelations
     * @param mixed ...$args
     *
     * @return array<IQueryRelation>
     */
    public function getRelations(Request $request, array $compositeRelations, ...$args): array;

    /**
     * Return a list of valid aggregate-able columns for this resource.
     * Other composites may influence the result when final
     * verb execution is reached.
     *
     * @param Request $request incoming request
     * @param array $compositeAggregations
     * @param mixed ...$args
     *
     * @return array
     */
    public function getAggregateOptions(Request $request, array $compositeAggregations, ...$args): array;

    /**
     * Called just before creating a query is used to retrieve records.
     * One should use this callback to
     *
     * @param Request $request incoming request
     * @param Builder $query Query that will be used
     * @param mixed ...$args incoming route args
     *
     * @throws ValidationException
     * @throws Exception
     *
     * @return void
     */
    public function onBeforeQuery(Request $request, Builder $query, ...$args): void;

    /**
     * Called after a model is successfully created in database.One can
     * use this composite callback to
     *
     * @param Request $request incoming request
     * @param Builder $query Query that produced paginatedResult
     * @param LengthAwarePaginator $paginatedResult query result
     * @param mixed ...$args incoming route args
     *
     * @throws ValidationException
     * @throws Exception
     *
     * @return void
     */
    public function onAfterQuery(Request $request, Builder $query, LengthAwarePaginator $paginatedResult, ...$args): void;
}
