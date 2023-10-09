<?php

namespace Roghumi\Press\Crud\Services\AccessService;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Roghumi\Press\Crud\Resources\Role\Permission;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;

class AccessService implements IAccessService
{
    /**
     * Insert permission names in database if they do not exist.
     *
     * @param  string[]  $providers
     * @return void
     */
    public function updatePermissionsTable(array $providers)
    {
        foreach ($providers as $provider) {
            if (is_string($provider)) {
                $provider = new $provider();
            }
            $verbs = $provider->getAvailableVerbAndCompositions();
            foreach ($verbs as $verbName => $compositions) {
                Permission::updateOrInsert([
                    'name' => $this->getPermissionNameFromVerb($provider->getName(), $verbName),
                ]);
            }
        }
    }

    /**
     * Get permission name for a given verb on a given resource.
     */
    public function getPermissionNameFromVerb(
        string $providerName,
        string $verbName
    ): string {
        $verbName = Str::lower($verbName);
        $providerName = Str::lower($providerName);

        return "crud.$verbName.$providerName";
    }

    /**
     * Create an array of action data for a crud route.
     * this data can be loaded and used on verb execution.
     */
    public function getCrudRouteMetadata(ICrudVerb $verb, ICrudResourceProvider $provider): array
    {
        return [
            'provider_class' => get_class($provider),
            'verb_class' => get_class($verb),
        ];
    }

    /**
     * Return if user has access to verb
     */
    public function hasAccessToVerb(
        IUser $user,
        string $providerName,
        string $verbName
    ): bool {
        $permissionName = $this->getPermissionNameFromVerb($providerName, $verbName);

        return $this->hasUserPermission($user, $permissionName);
    }

    /**
     * Return if user has all permissions.
     *
     * @param  string|string[]  $permissionName
     */
    public function hasUserPermission(
        IUser $user,
        mixed $permissionName
    ): bool {
        return $user->getPermissionNames()->contains($permissionName);
    }

    /**
     * Return if this is a valid crud route.
     */
    public function isValidCrudRoute(Route $route): bool
    {
        if (Str::startsWith($route->getName(), 'crud.')) {
            $providerClass = $route->getAction('provider_class');
            $verbClass = $route->getAction('verb_class');

            return class_exists($providerClass) && class_exists($verbClass);
        }

        return false;
    }

    /**
     * Get resource provider associated with this route.
     *
     *
     * @return ICrudResourceProvider
     */
    public function getProviderFromRoute(Route $route): ?ICrudResourceProvider
    {
        $providerClass = $route->getAction('provider_class');
        if (! is_null($providerClass)) {
            return new $providerClass();
        }

        return null;
    }

    /**
     * Get verb associated with this route.
     *
     *
     * @return ICrudVerb
     */
    public function getVerbFromRoute(Route $route): ?ICrudVerb
    {
        $verbClass = $route->getAction('verb_class');
        if (! is_null($verbClass)) {
            return new $verbClass();
        }

        return null;
    }

    /**
     * Get resource provider class associated with this route.
     */
    public function getProviderClassFromRoute(Route $route): ?string
    {
        return $route->getAction('provider_class');
    }

    /**
     * Get verb class associated with this route.
     */
    public function getVerbClassFromRoute(Route $route): ?string
    {
        return $route->getAction('verb_class');
    }
}
