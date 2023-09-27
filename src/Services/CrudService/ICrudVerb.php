<?php

namespace Roghumi\Press\Crud\Services\CrudService;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

interface ICrudVerb
{
    /**
     * Verb name used for RBAC
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Generate and register a new route based on a crud resource provider
     *
     * @param ICrudResourceProvider $provider resource provider to use for route generation.
     *
     * @return Route
     */
    public function getRouteForResource(ICrudResourceProvider $provider): Route;

    /**
     * execute the verbs logic with a provider and request
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
    public function execRequest(Request $request, ICrudResourceProvider $provider, ...$args): mixed;

    /**
     * get sanitized output for this request
     *  params is an array of params from @exec function appended with result of @exec at end
     *
     * @param Request $request incoming request
     * @param mixed $execResult output response from execRequest
     *
     * @return array
     */
    public function getSanitizedOutput(Request $request, mixed $execResult): array;
}
