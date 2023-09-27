<?php

namespace Roghumi\Press\Crud\Services\CrudService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Crud Service.
 * Register routes for your Resource providers and execute them.
 */
interface ICrudService
{
    /**
     * Register a route for each verb of these resource provider classes.
     *
     * @param string[] $providerClassNames
     *
     * @return void
     */
    public function registerCrudRoutes(array $providerClassNames): void;

    /**
     * Execute a verb and return response
     *
     * @param ICrudVerb $verb
     * @param ICrudResourceProvider $provider
     * @param Request $request
     * @param mixed ...$args
     *
     * @return JsonResponse
     */
    public function executeVerbForResource(
        ICrudVerb $verb,
        ICrudResourceProvider $provider,
        Request $request,
        ...$args
    ): JsonResponse;
}