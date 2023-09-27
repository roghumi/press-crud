<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Update;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;
use Roghumi\Press\Crud\Exceptions\ResourceNotFoundException;
use Roghumi\Press\Crud\Services\AccessService\Traits\RBACVerbTrait;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;

class Update implements ICrudVerb
{
    use RBACVerbTrait;

    const NAME = 'update';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getRouteForResource(ICrudResourceProvider $provider): Route
    {
        return $this->registerRouteWithControl(
            $provider,
            ['POST'],
            sprintf('%s/{id}/update', $provider->getName()),
        );
    }

    /**
     * Undocumented function
     *
     * @throws Exception
     * @throws ValidationException
     *
     * @dispatches CreateEvent
     */
    public function execRequest(Request $request, ICrudResourceProvider $provider, ...$args): mixed
    {
        return $this->execRouteWithControl(
            $request,
            $provider,
            // verb execution callback
            // real part of verb execution
            function (array $sanitizedData, array $verbCompositions) use ($request, $args, $provider) {
                $modelId = $args[0];

                $model = $provider->getObjectById($modelId)?->getModel();

                if (is_null($model)) {
                    throw new ResourceNotFoundException($modelId, $provider::class);
                }

                foreach ($verbCompositions as $verbComposition) {
                    if ($verbComposition instanceof IUpdateVerbComposite) {
                        $verbComposition->onBeforeUpdate($request, $model, ...$args);
                    }
                }

                $model->update($sanitizedData);

                foreach ($verbCompositions as $verbComposition) {
                    if ($verbComposition instanceof IUpdateVerbComposite) {
                        $verbComposition->onAfterUpdate($request, $model, ...$args);
                    }
                }

                return $model;
            },
            // verb dispatch events callback
            function ($model) use ($provider) {
                UpdateEvent::dispatch(get_class($provider), $model->id, time());
            },
            // custom composite callback
            null,
            // validate on rules
            true,
            // use db transactions
            true,
            // pass args
            ...$args
        );
    }

    /**
     * Undocumented function
     *
     * @param  array  $params
     */
    public function getSanitizedOutput(Request $request, mixed $params): array
    {
        /** @var Model $model */
        $model = $params;

        return [
            'message' => trans('press.crud.verbs.update.success', [
                'recordId' => $model->id,
            ]),
            'object' => $model->toArray(),
        ];
    }
}
