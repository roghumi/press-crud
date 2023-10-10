<?php

namespace Roghumi\Press\Crud\Services\AccessService\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Roghumi\Press\Crud\Exceptions\VerbNotFoundException;
use Roghumi\Press\Crud\Facades\AccessService;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerbComposite;

/**
 *  A trait for building standard RBAC verbs for any resource
 */
trait RBACVerbTrait
{
    /**
     * helper function for executing Verbs,
     * all verb implementations should use this to improve security and performance
     * use $executeCallback to modify whatever you want and return a resulting object
     * use $dispatchEvensCallback to call events after successful verb execution,
     * you will receive your resulting object from $executeCallback as a parameter
     *
     * @param  callable  $executeCallback callback function with
     *    signature (array $sanitizedData, array $verbCompositions): any as $execResult
     * @param  callable  $dispatchEvensCallback callback function with signature (any $execResult)
     * @param  callable|null  $customCompositeCallback callback function with signature (ICrudVerbComposite $composite)
     * @param  bool|callable  $verifyRules boolean or function with
     *    signature (ICrudResourceProvider $provider, ICrudVerbComposite[] $verbCompositions, array $compositionRules)
     * @param  bool  $useTransactions wrap callbacks in a db transaction call
     * @param  mixed  ...$args args from route
     */
    protected function execRouteWithControl(
        Request $request,
        ICrudResourceProvider $provider,
        callable $executeCallback,
        callable $dispatchEvensCallback,
        ?callable $customCompositeCallback,
        bool|callable $verifyRules,
        bool $useTransactions,
        ...$args,
    ): mixed {
        $verbComposMap = $provider->getAvailableVerbAndCompositions();
        /** @var ICrudVerbComposite[] $verbCompositions */
        $verbCompositions = $verbComposMap[$this->getName()] ?? null;
        if (is_null($verbCompositions) || ! is_array($verbCompositions)) {
            throw new VerbNotFoundException($this->getName());
        }

        $sanitizedData = [];
        if ($verifyRules) {
            $verifyRulesList = [];
            foreach ($verbCompositions as $composite) {
                $verifyRulesList = $composite->getRules($request, $verifyRulesList, ...$args);
                $sanitizedData = $composite->getSanitized($request, $sanitizedData, ...$args);
                if (! is_null($customCompositeCallback)) {
                    $customCompositeCallback($composite);
                }
            }
            if (is_callable($verifyRules)) {
                $verifyRulesList = $verifyRules($verifyRulesList);
            }

            $validator = Validator::make($request->all(), $verifyRulesList);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        } else {
            foreach ($verbCompositions as $composite) {
                $sanitizedData = $composite->getSanitized($request, $sanitizedData, ...$args);
                if (! is_null($customCompositeCallback)) {
                    $customCompositeCallback($composite);
                }
            }
        }

        $execResult = null;
        // execute verb
        try {
            // wrap in transaction if asked
            if ($useTransactions) {
                DB::beginTransaction();
            }
            // call execution callback
            $execResult = $executeCallback($sanitizedData, $verbCompositions);
            if ($useTransactions) {
                DB::commit();
            }
        } catch (\Exception $e) {
            if ($useTransactions) {
                DB::rollback();
            }
            throw $e;
        }

        // dispatch events on successful verb execution
        $dispatchEvensCallback($execResult);

        // create a sanitized response json
        return $this->getSanitizedOutput($request, $execResult);
    }

    /**
     * register a route for this verb and provider
     * combine this method with execRouteWithControl() to achieve safe custom crud actions
     */
    protected function registerRouteWithControl(
        ICrudResourceProvider $provider,
        array $methods,
        string $url
    ): Route {
        $verbName = Str::lower($this->getName());
        $name = AccessService::getPermissionNameFromVerb($provider->getName(), $verbName);

        return RouteFacade::match(
            $methods,
            $url,
            array_merge(AccessService::getCrudRouteMetadata($this, $provider), [
                'uses' => self::class.'@executeVerbFromRoute',
            ])
        )->name($name);
    }

    /**
     * Execute the verb from route.
     * This function acts as controller entry point.
     * Get associated provider for the verb and pass args to get the response.
     *
     * @param  Request  $request incoming request.
     * @param  Route  $route route registered for this verb.
     * @param  mixed  ...$args route args.
     * @return JsonResponse
     */
    public function executeVerbFromRoute(Request $request, Route $route, ...$args)
    {
        return response()->json(
            $this->execRequest(
                $request,
                AccessService::getProviderFromRoute($route),
                ...$args
            )
        );
    }
}
