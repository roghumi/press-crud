<?php

namespace Roghumi\Press\Crud\Services\CrudService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrudService implements ICrudService
{
    /**
     * Undocumented function
     *
     * @param  array<ICrudResourceProvider>  $providers
     * @return null
     */
    public function registerCrudRoutes(array $providers): void
    {
        /** @var ICrudResourceProvider[] */
        $providerRefs = [];
        foreach ($providers as $provider) {
            if (is_string($provider)) {
                $providerRefs[] = new $provider();
            } elseif ($provider instanceof ICrudResourceProvider) {
                $providerRefs[] = $provider;
            }
        }

        $availableVerbClasses = config('press.crud.verbs');
        /** @var ICrudVerb[] */
        $verbInstances = [];
        foreach ($availableVerbClasses as $verbClass) {
            /** @var ICrudVerb */
            $verb = new $verbClass();
            $verbInstances[$verb->getName()] = $verb;
        }

        foreach ($providerRefs as $provider) {
            $verbs = $provider->getAvailableVerbAndCompositions();
            foreach ($verbs as $verbName => $compositions) {
                if (isset($verbInstances[$verbName])) {
                    $verbInstances[$verbName]->getRouteForResource($provider);
                }
            }
        }
    }

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
    ): JsonResponse {
        return response()->json($verb->execRequest(
            $request,
            $provider,
            ...$args,
        ));
    }
}
