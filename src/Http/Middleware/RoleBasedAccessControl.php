<?php

namespace Roghumi\Press\Crud\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Roghumi\Press\Crud\Exceptions\AccessDeniedException;
use Roghumi\Press\Crud\Exceptions\NotACrudRouteException;
use Roghumi\Press\Crud\Facades\AccessService;

/**
 * RBAC Route middleware
 * Use this middleware on all your crud resource routes.
 * This middleware will check if authenticated user has access
 * to execute this route, domain and group access are not accounted for here.
 */
class RoleBasedAccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Route */
        $route = $request->route();
        if (is_null($route)) {
            throw new NotACrudRouteException();
        }
        if (! AccessService::isValidCrudRoute($route)) {
            throw new NotACrudRouteException();
        }
        $user = Auth::user();
        if (is_null($user)) {
            throw new UnauthorizedException();
        }

        $provider = AccessService::getProviderFromRoute($route);
        $verb = AccessService::getVerbFromRoute($route);

        if (! AccessService::hasAccessToVerb($user, $provider->getName(), $verb->getName())) {
            throw new AccessDeniedException(trans('press.crud.exceptions.access_denied', [
                'class' => $provider->getName(),
                'verb' => $verb->getName(),
            ]));
        }

        return $next($request);
    }
}
