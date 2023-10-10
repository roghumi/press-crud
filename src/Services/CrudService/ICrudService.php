<?php

namespace Roghumi\Press\Crud\Services\CrudService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Crud Service.
 * Register routes for your Resource providers and execute them.
 */
interface ICrudService
{
    /**
     * Register a route for each verb of these resource provider classes.
     *
     * @param  array<ICrudResourceProvider>  $providerClassNames
     */
    public function registerCrudRoutes(array $providerClassNames): void;

    /**
     * Execute a verb and return response
     *
     * @param  mixed  ...$args
     */
    public function executeVerbForResource(
        ICrudVerb $verb,
        ICrudResourceProvider $provider,
        Request $request,
        ...$args
    ): JsonResponse;
}
