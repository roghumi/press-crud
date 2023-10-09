<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Duplicate;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Roghumi\Press\Crud\Exceptions\ResourceNotFoundException;
use Roghumi\Press\Crud\Resources\Domain\DuplicateComposite;
use Roghumi\Press\Crud\Services\AccessService\Traits\RBACVerbTrait;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;

/**
 * Duplicate verb class.
 * Duplicates any kind of resource with a given ICrudResourceProvider
 */
class Duplicate implements ICrudVerb
{
    use RBACVerbTrait;

    public const NAME = 'duplicate';

    /**
     * Verb name used for RBAC
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * Register a route corresponding this verb on a provided resource provider.
     * Duplicate route params:
     *  index[0] = source record id
     *  index[1] = count of duplicates (optional)
     *
     * @param  ICrudResourceProvider  $provider resource provider to use for route generation.
     */
    public function getRouteForResource(ICrudResourceProvider $provider): Route
    {
        return $this->registerRouteWithControl(
            $provider,
            ['POST'],
            /**
             * {id} is request index 0 arg
             * {count} is request index 1 arg
             */
            sprintf('%s/{id}/duplicate/{count?}', $provider->getName())
        )->where('count', '[0-9]+');
    }

    /**
     * Execute verb based on request and resource provider
     * you must enter valid $args as they are defined by getRouteForResource
     *
     * @param  Request  $request Incoming request.
     * @param  ICrudResourceProvider  $provider Resource provider to use.
     * @param  mixed  ...$args Other Parameters of this verb, defined in route registration function most of the times.
     *
     * @throws Exception
     * @throws ValidationException
     *
     * @dispatches DeleteEvent
     */
    public function execRequest(Request $request, ICrudResourceProvider $provider, ...$args): mixed
    {
        return $this->execRouteWithControl(
            $request,
            $provider,
            // verb execution callback
            // real part of verb execution
            function (array $sanitizedData, array $verbCompositions) use ($request, $args, $provider) {
                $modelId = $args[0]; // arg 0 = model id
                $count = 1;
                if (isset($args[1])) {
                    $count = $args[1]; // optional arg 1 = duplicate count
                }

                $source = $provider->getObjectById($modelId)?->getModel();
                $sourceData = $source?->toArray();

                if (is_null($source)) {
                    throw new ResourceNotFoundException($modelId, $provider::class);
                }

                $targets = new Collection([]);

                for ($i = 0; $i < $count; $i++) {
                    $targets->push($provider->generateModelFromData($sourceData));
                }

                foreach ($verbCompositions as $verbComposition) {
                    if ($verbComposition instanceof DuplicateComposite) {
                        $verbComposition->onBeforeDuplicate($request, $source, $targets, ...$args);
                    }
                }

                // insert one by one to get ids
                foreach ($targets as $target) {
                    $target->save();
                }

                foreach ($verbCompositions as $verbComposition) {
                    if ($verbComposition instanceof DuplicateComposite) {
                        $verbComposition->onAfterDuplicate($request, $source, $targets, ...$args);
                    }
                }

                return [$source, $targets];
            },
            // verb dispatch events callback
            function ($result) use ($provider) {
                /** @var Model */
                $source = $result[0];
                /** @var Collection */
                $duplicates = $result[1];

                DuplicateEvent::dispatch(
                    Auth::user()?->id ?? null,
                    get_class($provider),
                    $source->id,
                    $duplicates->pluck('id')->toArray(),
                    time()
                );
            },
            // custom composite callback
            null,
            // validate on rules
            true,
            // use db transactions
            true,
            // args
            ...$args
        );
    }

    /**
     * get sanitized output for this request
     *  params is an array of params from @exec function appended with result of @exec at end
     *
     * @param  Request  $request incoming request
     * @param  mixed  $execResult output response from execRequest
     */
    public function getSanitizedOutput(Request $request, mixed $execResult): array
    {
        /** @var Model */
        $source = $execResult[0];
        /** @var Collection */
        $duplicates = $execResult[1];

        return [
            'message' => trans('press.crud.verbs.duplicate.success', [
                'sourceId' => $source->id,
                'duplicateId' => $duplicates->pluck('id')->join(','),
            ]),
            'items' => $duplicates->toArray(),
        ];
    }
}
