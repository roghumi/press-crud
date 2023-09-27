<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Export;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Roghumi\Press\Crud\Services\AccessService\Traits\RBACVerbTrait;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Query;

class Export extends Query
{
    use RBACVerbTrait;

    public const NAME = 'export';

    /**
     * Verb name used for RBAC
     *
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * Generate and register a new route based on a crud resource provider
     *
     * @param ICrudResourceProvider $provider resource provider to use for route generation.
     *
     * @return Route
     */
    public function getRouteForResource(ICrudResourceProvider $provider): Route
    {
        return $this->registerRouteWithControl(
            $provider,
            ['POST'],
            sprintf('%s/{id}/export/{format}', $provider->getName())
        )->whereIn('format', array_keys(config('press.crud.query.export_formatters')));
    }

    /**
     * execute crud verb with a request and resource provider
     *
     * @param Request $request Incoming request.
     * @param ICrudResourceProvider $provider Resource provider to use.
     * @param mixed ...$args Other Parameters of this verb, defined in route registration function most of the times.
     *
     * @throws ValidationException Will throw validation exception if request does not comply with verbs compositions.
     * @throws Exception Other general exceptions.
     *
     * @return mixed
     */
    public function execRequest(Request $request, ICrudResourceProvider $provider, ...$args): mixed
    {
        /** @var LengthAwarePaginator $lengthAware */
        $lengthAware = parent::execRequest($request, $provider, ...$args);
    }

    /**
     * Undocumented function
     *
     * @param Request $request incoming request
     * @param mixed $resultSet output response from execRequest
     *
     * @return array
     */
    public function getSanitizedOutput(Request $request, mixed $resultSet): array
    {
        /** @var LengthAwarePaginator $response */
        $response = $resultSet;

        return [
            'message' => trans('press.crud.verbs.query.success', [
                'count' => $response->count(),
            ]),
            'data' => $response->items(),
            'perPage' => $response->perPage(),
            'page' => $response->currentPage(),
            'total' => $response->total(),
            'count' => $response->count(),
            'rc' => $request->get('rc', Str::random(8)),
        ];
    }
}
