<?php

namespace Roghumi\Press\Crud\Resources\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Roghumi\Press\Crud\Services\AccessService\IUser;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Columns\IQueryColumn;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Columns\QueryColumn;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnBetween;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnEquals;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\IQueryVerbComposite;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\IQueryRelation;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryBelongsToRelation;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Traits\QueryCompositeTrait;

/**
 * Domain query composite.
 */
class QueryComposite implements IQueryVerbComposite
{
    // use standard getSanitized, getRules for query compositions
    use QueryCompositeTrait;

    /**
     * Return a list of valid columns for this resource.
     * Other composites may influence the result when final
     * verb execution is reached.
     *
     * @param  Request  $request incoming request
     * @param  array  $compositeColumns composition values
     * @param  mixed  ...$args
     * @return array<IQueryColumn>
     */
    public function getColumns(Request $request, array $compositeColumns, ...$args): array
    {
        return [
            QueryColumn::create('id', true),
            QueryColumn::create('name', true),
            QueryColumn::create('author_id', true),
            QueryColumn::create('data', false),
            QueryColumn::create('created_at', true),
            QueryColumn::create('updated_at', true),
            QueryColumn::create('deleted_at', true),
        ];
    }

    /**
     * Return a list of valid filters for this resource.
     * Other composites may influence the result when final
     * verb execution is reached.
     *
     * @param  Request  $request incoming request
     * @param  array  $compositeFilters composition values
     * @param  mixed  ...$args
     * @return array<IQueryFilter>
     */
    public function getFilters(Request $request, array $compositeFilters, ...$args): array
    {
        return [
            WhereColumnEquals::create('id.equals', 'id'),
            WhereColumnBetween::create('id.between', 'id'),
            WhereColumnEquals::create('name.equals', 'name'),
            WhereColumnEquals::create('author_id.equals', 'author_id'),
            WhereColumnBetween::create('created_at.between', 'created_at'),
            WhereColumnBetween::create('updated_at.between', 'updated_at'),
            WhereColumnBetween::create('deleted_at.between', 'deleted_at'),
        ];
    }

    /**
     * Return a list of valid relations for this resource.
     * Other composites may influence the result when final
     * verb execution is reached.
     *
     * @param  Request  $request incoming request
     * @param  mixed  ...$args
     * @return array<IQueryRelation>
     */
    public function getRelations(Request $request, array $compositeRelations, ...$args): array
    {
        return [
            QueryBelongsToRelation::create('author', config('press.crud.user.provider'), $request, ...$args),
        ];
    }

    /**
     * Return a list of valid aggregate-able columns for this resource.
     * Other composites may influence the result when final
     * verb execution is reached.
     *
     * @param  Request  $request incoming request
     * @param  mixed  ...$args
     */
    public function getAggregateOptions(Request $request, array $compositeAggregations, ...$args): array
    {
        return [
        ];
    }

    /**
     * Called just before creating a query is used to retrieve records.
     * One should use this callback to
     *
     * @param  Request  $request incoming request
     * @param  Builder  $query Query that will be used
     * @param  mixed  ...$args incoming route args
     *
     * @throws ValidationException
     * @throws Exception
     */
    public function onBeforeQuery(Request $request, Builder $query, ...$args): void
    {
        /** @var IUser $user */
        $user = Auth::user();

        // only allow super admin roles to see all domains
        // other roles will see their authored domains only.
        if (! $user->getTopRole()?->isSuperAdmin()) {
            $query->where('author_id', $user->id);
        }
    }

    /**
     * Called after a model is successfully created in database.One can
     * use this composite callback to
     *
     * @param  Request  $request incoming request
     * @param  Builder  $query Query that produced paginatedResult
     * @param  LengthAwarePaginator  $paginatedResult query result
     * @param  mixed  ...$args incoming route args
     *
     * @throws ValidationException
     * @throws Exception
     */
    public function onAfterQuery(Request $request, Builder $query, LengthAwarePaginator $paginatedResult, ...$args): void
    {
    }
}
