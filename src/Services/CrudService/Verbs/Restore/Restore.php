<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Restore;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;
use Roghumi\Press\Crud\Exceptions\ResourceNotFoundException;
use Roghumi\Press\Crud\Helpers\UserHelpers;
use Roghumi\Press\Crud\Services\AccessService\Traits\RBACVerbTrait;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;

class Restore implements ICrudVerb
{
    use RBACVerbTrait;

    public const NAME = 'restore';

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
            sprintf('%s/{id}/restore', $provider->getName()),
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
            // real part of verb execution
            function (array $sanitizedData, array $verbCompositions) use ($request, $args, $provider) {
                $modelId = $args[0]; // arg index 0 = model id

                $model = $provider->getObjectById($modelId)?->getModel();

                if (is_null($model)) {
                    throw new ResourceNotFoundException($modelId, $provider::class);
                }

                foreach ($verbCompositions as $verbComposition) {
                    if ($verbComposition instanceof IRestoreVerbComposite) {
                        $verbComposition->onBeforeRestore($request, $model, ...$args);
                    }
                }

                $model->restore();

                foreach ($verbCompositions as $verbComposition) {
                    if ($verbComposition instanceof IRestoreVerbComposite) {
                        $verbComposition->onAfterRestore($request, $model, ...$args);
                    }
                }

                return $model;
            },
            // verb dispatch events callback
            function ($model) use ($provider) {
                RestoreEvent::dispatch(UserHelpers::getAuthUserId(), get_class($provider), $model->id, time());
            },
            // custom composite callback
            null,
            // validate on rules
            true,
            // use db transactions
            false
        );
    }

    /**
     * Undocumented function
     *
     * @param  Request  $request incoming request
     * @param  mixed  $params output response from execRequest
     */
    public function getSanitizedOutput(Request $request, mixed $params): array
    {
        /** @var Model $model */
        $model = $params;

        return [
            'message' => trans('press.crud.verbs.undo_delete.success', [
                'recordId' => $model->id,
            ]),
            'object' => $model->toArray(),
        ];
    }
}
