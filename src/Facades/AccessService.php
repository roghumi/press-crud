<?php

namespace Roghumi\Press\Crud\Facades;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Facade;
use Roghumi\Press\Crud\Services\AccessService\IAccessService;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;

/**
 * Press AccessServices Facade
 *
 * @method static void updatePermissionsTable(array $providers)
 * @method static string getPermissionNameFromVerb(string $providerName, string $verbName)
 * @method static bool hasAccessToVerb(IUser $user, string $providerName, string $verbName)
 * @method static bool hasUserPermission(IUser $user, string $permissionName)
 * @method static ICrudResourceProvider getProviderFromRoute(Route $route)
 * @method static ICrudVerb getVerbFromRoute(Route $route)
 * @method static bool isValidCrudRoute(Route $route)
 * @method static string getProviderClassFromRoute(Route $route)
 * @method static string getVerbClassFromRoute(Route $route)
 * @method static array getCrudRouteMetadata(ICrudVerb $verb, ICrudResourceProvider $provider)
 */
class AccessService extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return IAccessService::class;
    }
}
