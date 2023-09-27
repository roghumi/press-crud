<?php

namespace Roghumi\Press\Crud\Facades;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Facade;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudService;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;

/**
 * Press CrudServices Facade
 *
 * @method static Response|JsonResponse executeVerbForResource(ICrudVerb $verb, ICrudResourceProvider $provider, Request $request, ...$args)
 */
class CrudService extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return ICrudService::class;
    }
}
