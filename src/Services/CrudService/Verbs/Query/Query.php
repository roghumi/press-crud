<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Roghumi\Press\Crud\Helpers\UserHelpers;
use Roghumi\Press\Crud\Services\AccessService\Traits\RBACVerbTrait;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Columns\IQueryColumn;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\IQueryFilter;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\IQueryRelation;
use Roghumi\Press\Crud\Validations\JsonKeysIn;

/**
 * Query verb class.
 * Executes query request and returns LengthAwarePaginator as response.
 * Request rules is dictated by provider class corresponding Composite to Query verb.
 * Adds automatic column, relation, filter, sort validation based on provider class Composites.
 * Use QueryCompositeTrait on your resource composites to prepare rule
 */
class Query implements ICrudVerb
{
    use RBACVerbTrait;

    public const NAME = 'query';

    /**
     * Verb name used for RBAC
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * Generate and register a new route based on a crud resource provider
     *
     * @param  ICrudResourceProvider  $provider resource provider to use for route generation.
     */
    public function getRouteForResource(ICrudResourceProvider $provider): Route
    {
        return $this->registerRouteWithControl(
            $provider,
            ['POST'],
            sprintf('%s/query', $provider->getName()),
        );
    }

    /**
     * execute crud verb with a request and resource provider
     *
     * @param  Request  $request Incoming request.
     * @param  ICrudResourceProvider  $provider Resource provider to use.
     * @param  mixed  ...$args Other Parameters of this verb, defined in route registration function most of the times.
     *
     * @throws ValidationException Will throw validation exception if request does not comply with verbs compositions.
     * @throws Exception Other general exceptions.
     */
    public function execRequest(Request $request, ICrudResourceProvider $provider, ...$args): mixed
    {
        // collect composite query params
        $validColumns = [];
        $validRelations = [];
        $validFilters = [];

        return $this->execRouteWithControl(
            $request,
            $provider,
            // verb execution callback
            // real part of verb execution
            function (
                array $sanitizedData,
                array $verbCompositions
            ) use (
                $request,
                $args,
                $provider,
                &$validRelations,
                &$validColumns,
                &$validFilters,
            ) {
                $includeTrash = $request->get('includeTrash', false);
                $limitPerPage = $request->get('perPage', config('press.crud.query.perPage'));
                $pageIndex = min(0, intval($request->get('page', 1)) - 1);
                /** @var Builder */
                $query = call_user_func([$provider->getModelClass(), 'query']);

                if ($includeTrash) {
                    $query->includeTrash();
                }

                /** @var IQueryFilter[] */
                $activeFilters = $this->getEloquentFilters(
                    $request->get('filters', []),
                    (new Collection($validFilters))->keyBy('name'),
                );
                /**
                 * @var IQueryFilter $filterDef
                 * @var Closure $filterFunction
                 */
                foreach ($activeFilters as $filterName => [$filterDef, $filterFunction]) {
                    $filterDef->applyFilter($filterFunction, $query);
                }

                $queryTotalCount = $query->count();
                $query->skip($limitPerPage * $pageIndex);
                $query->limit($limitPerPage);

                /** @var IQueryRelation[] */
                $activeRelations = $this->getEloquentRelations(
                    $request->get('relations', []),
                    (new Collection($validRelations))->keyBy('name'),
                );
                /**
                 * @var IQueryRelation $relationDef
                 * @var Closure $eagerFunction
                 */
                foreach ($activeRelations as $relationName => [$relationDef, $eagerFunction]) {
                    $query->with([$relationName => $eagerFunction]);
                }

                $activeColumns = $this->getEloquentColumns(
                    $request->get('columns', []),
                    (new Collection($validColumns))->keyBy('name'),
                );
                if (count($activeColumns) > 0) {
                    $query->select(array_keys($activeColumns));
                }

                $sortRequestCollection = Collection::make($request->get('sortBy', []));
                /** @var IQueryColumn[] $sortColumns */
                $sortColumns = $this->getEloquentColumns(
                    $sortRequestCollection->keys()->toArray(),
                    Collection::make(($validColumns))->keyBy('name'),
                );
                foreach ($sortColumns as $sortColumn) {
                    $sortColumn->sortQueryOnColumn($query, $sortRequestCollection->get($sortColumn->getColumn()));
                }

                foreach ($verbCompositions as $verbComposition) {
                    if ($verbComposition instanceof IQueryVerbComposite) {
                        $verbComposition->onBeforeQuery($request, $query, ...$args);
                    }
                }

                $paginatedResponse = new LengthAwarePaginator(
                    $query->get()->toArray(),
                    $queryTotalCount,
                    $limitPerPage,
                    $pageIndex
                );

                foreach ($verbCompositions as $verbComposition) {
                    if ($verbComposition instanceof IQueryVerbComposite) {
                        $verbComposition->onAfterQuery($request, $query, $paginatedResponse, ...$args);
                    }
                }

                return $paginatedResponse;
            },
            // verb dispatch events callback
            function ($result) use ($provider) {
                QueryEvent::dispatch(UserHelpers::getAuthUserId(), get_class($provider), time());
            },
            // custom composite callback
            function ($composite) use (
                $request,
                &$validRelations,
                &$validColumns,
                &$validFilters,
                $args,
            ) {
                if ($composite instanceof IQueryVerbComposite) {
                    $validColumns = $composite->getColumns($request, $validColumns, ...$args);
                    $validRelations = $composite->getRelations($request, $validRelations, ...$args);
                    $validFilters = $composite->getFilters($request, $validFilters, ...$args);
                }
            },
            // validate on rules
            function ($compositeRules) use (
                $request,
                &$validRelations,
                &$validColumns,
                &$validFilters,
                $args,
            ) {
                $compositeRules['columns'] = array_merge(
                    $compositeRules['columns'] ?? [],
                    [Rule::in(Collection::make($validColumns)->keyBy('name')->keys()->toArray())]
                );
                $compositeRules['relations'] = array_merge(
                    $compositeRules['relations'] ?? [],
                    [new JsonKeysIn(Collection::make($validRelations)->keyBy('name'))]
                );
                $compositeRules['filters'] = array_merge(
                    $compositeRules['filters'] ?? [],
                    [new JsonKeysIn(Collection::make($validFilters)->keyBy('name'))]
                );
                $compositeRules['sortBy'] = array_merge(
                    $compositeRules['sortBy'] ?? [],
                    [new JsonKeysIn(Collection::make($validColumns)->filter(
                        function (IQueryColumn $column) use ($request, $args) {
                            return $column->isSortable($request, ...$args);
                        }
                    )->keyBy('name'))]
                );

                return $compositeRules;
            },
            // use db transactions
            false,
            // pass args
            ...$args
        );
    }

    /**
     * get sanitized output for this verb
     *
     * @param  Request  $request incoming request
     * @param  mixed  $resultSet output response from execRequest
     */
    public function getSanitizedOutput(Request $request, mixed $resultSet): array
    {
        /** @var LengthAwarePaginator $response */
        $response = $resultSet;

        return [
            'message' => trans('press.crud.verbs.query.success', [
                'count' => $response->count(),
                'total' => $response->total(),
            ]),
            'items' => $response->items(),
            'perPage' => $response->perPage(),
            'page' => $response->currentPage(),
            'total' => $response->total(),
            'count' => $response->count(),
            'rc' => $request->get('rc', Str::random(8)),
        ];
    }

    /**
     * get a list of callbacks for applying requested $relations from $validRelations list
     *
     * @param  array  $relations request relations as described in QueryCompositeTrait
     * @param  Collection  $validRelations available relations
     * @return IQueryRelation[]
     */
    protected function getEloquentRelations(array $relations, Collection $validRelations): array
    {
        $eloquentRelations = [];
        foreach ($relations as $relationName => $relationDetails) {
            /** @var IQueryRelation $relationDef */
            $relationDef = $validRelations->get($relationName);
            if ($relationDef->validateRelationRequestParams($relationDetails)) {
                $eloquentRelations[$relationName] = [
                    $relationDef,
                    $relationDef->createEagerLoadFunctionForRelation($relationDetails),
                ];
            }
        }

        return $eloquentRelations;
    }

    /**
     * get a list of callbacks for applying requested $filters from $validFilters list
     *
     * @param  array  $filters request filters as described in QueryCompositeTrait
     * @param  Collection  $validFilters available filters for resource
     * @return IQueryFilter[]
     */
    protected function getEloquentFilters(array $filters, Collection $validFilters): array
    {
        $eloquentFilters = [];
        foreach ($filters as $filterName => $filterDetails) {
            /** @var IQueryFilter $filterDef */
            $filterDef = $validFilters->get($filterName);
            if ($filterDef->validateFilterRequestParams($filterDetails)) {
                $eloquentFilters[$filterName] = [
                    $filterDef,
                    $filterDef->createFilterFunctionForRequestParams($filterDetails),
                ];
            }
        }

        return $eloquentFilters;
    }

    /**
     * get a list of callbacks for applying requested $column selection from $validColumns list
     *
     * @param  array  $columns request columns as described in QueryCompositeTrait
     * @param  Collection  $validColumns available columns
     * @return IQueryColumn[]
     */
    protected function getEloquentColumns(array $columns, Collection $validColumns): array
    {
        $eloquentColumns = [];
        foreach ($columns as $columnName) {
            /** @var IQueryColumn $filterDef */
            $columnDef = $validColumns->get($columnName);
            $eloquentColumns[$columnName] = $columnDef;
        }

        return $eloquentColumns;
    }
}
