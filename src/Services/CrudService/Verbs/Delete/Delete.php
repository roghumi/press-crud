<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Delete;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;
use Roghumi\Press\Crud\Exceptions\ResourceNotFoundException;
use Roghumi\Press\Crud\Helpers\UserHelpers;
use Roghumi\Press\Crud\Services\AccessService\Traits\RBACVerbTrait;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;

/**
 * Delete verb.
 * Can delete any resource with corresponding provider.
 * The resource provider will provide ICrudVerbComposition to
 * be used for rule verification and sanitization, also
 * before and after callbacks.
 */
class Delete implements ICrudVerb
{
    use RBACVerbTrait;

    public const NAME = 'delete';

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
            ['DELETE'],
            sprintf('%s/{id}', $provider->getName())
        );
    }

    /**
     * execute the verbs logic with a provider and request
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
        return $this->execRouteWithControl(
            $request,
            $provider,
            // verb execution callback
            function (array $sanitizedData, array $verbCompositions) use ($request, $args, $provider) {
                $modelId = $args[0]; // arg index 0 = model id
                // real part of verb execution
                $model = $provider->getObjectById($modelId)?->getModel();

                if (is_null($model)) {
                    throw new ResourceNotFoundException($modelId, $provider::class);
                }

                foreach ($verbCompositions as $composite) {
                    if ($composite instanceof IDeleteVerbComposite) {
                        $composite->onBeforeDelete($request, $model, ...$args);
                    }
                }

                $model->delete();

                foreach ($verbCompositions as $composite) {
                    if ($composite instanceof IDeleteVerbComposite) {
                        $composite->onAfterDelete($request, $model, ...$args);
                    }
                }

                return $model;
            },
            // verb dispatch events callback
            function ($model) use ($provider) {
                DeleteEvent::dispatch(
                    UserHelpers::getAuthUserId(),
                    get_class($provider),
                    $model->id,
                    time()
                );
            },
            // custom composite callback
            null,
            // validate on rules
            true,
            // use db transactions
            false,
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
        /** @var Model $model */
        $model = $execResult;

        return [
            'message' => trans('press.crud.verbs.delete.success', [
                'recordId' => $model->id,
            ]),
            'object' => $model->toArray(),
        ];
    }
}
