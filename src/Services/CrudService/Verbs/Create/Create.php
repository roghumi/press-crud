<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Create;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Roghumi\Press\Crud\Services\AccessService\Traits\RBACVerbTrait;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;

/**
 * Create verb.
 * Can create any resource with corresponding provider.
 * The resource provider will provide ICrudVerbComposition to
 * be used for rule verification and sanitization, also
 * before and after callbacks.
 *
 */
class Create implements ICrudVerb
{
    use RBACVerbTrait;

    public const NAME = 'create';

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
            sprintf('%s', $provider->getName())
        );
    }

    /**
     * Undocumented function
     *
     * @param Request $request Incoming request.
     * @param ICrudResourceProvider $provider Resource provider to use.
     * @param mixed ...$args Other Parameters of this verb, defined in route registration function most of the times.
     *
     * @throws Exception
     * @throws ValidationException
     *
     * @dispatches CreateEvent
     * @return mixed
     */
    public function execRequest(Request $request, ICrudResourceProvider $provider, ...$args): mixed
    {
        return $this->execRouteWithControl(
            $request,
            $provider,
            // verb execution callback
            function (array $sanitizedData, array $verbCompositions) use ($request, $args, $provider) {
                // real part of verb execution
                $model = $provider->generateModelFromData($sanitizedData);

                foreach ($verbCompositions as $composite) {
                    if ($composite instanceof ICreateVerbComposite) {
                        $composite->onBeforeCreate($request, $model, ...$args);
                    }
                }

                $model->save();

                foreach ($verbCompositions as $composite) {
                    if ($composite instanceof ICreateVerbComposite) {
                        $composite->onAfterCreate($request, $model, ...$args);
                    }
                }

                return $model;
            },
            // verb dispatch events callback
            function ($model) use ($provider) {
                CreateEvent::dispatch(
                    Auth::user()?->id ?? null,
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
            true,
            // pass args
            ...$args
        );
    }

    /**
     * get sanitized output for this request
     *  params is an array of params from @exec function appended with result of @exec at end
     *
     * @param Request $request incoming request
     * @param mixed $execResult output response from execRequest
     *
     * @return array
     */
    public function getSanitizedOutput(Request $request, mixed $execResult): array
    {
        /** @var Model $model */
        $model = $execResult;

        return [
            'message' => trans('press.crud.verbs.create.success', [
                'recordId' => $model->id,
            ]),
            'object' => $model->toArray(),
        ];
    }
}
