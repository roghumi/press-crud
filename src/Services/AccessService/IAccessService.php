<?php

namespace Roghumi\Press\Crud\Services\AccessService;

use Illuminate\Routing\Route;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;

/**
 * RBAC main access control service interface
 * This service is responsible for creating an
 * standard for RBAC verbs data retrieval and
 * permissions checking.
 */
interface IAccessService
{
    /**
     * Insert permission names in database if they do not exist.
     *
     * @param  string[]  $providers
     * @return void
     */
    public function updatePermissionsTable(array $providers);

    /**
     * Create an array of action data for a crud route.
     * this data can be loaded and used on verb execution.
     */
    public function getCrudRouteMetadata(ICrudVerb $verb, ICrudResourceProvider $provider): array;

    /**
     * Get permission name for a given verb on a given resource.
     */
    public function getPermissionNameFromVerb(
        string $providerName,
        string $verbName
    ): string;

    /**
     * Return if user has access to verb
     */
    public function hasAccessToVerb(
        IUser $user,
        string $providerName,
        string $verbName
    ): bool;

    /**
     * Return if user has all permissions.
     *
     * @param  string|string[]  $permissionName
     */
    public function hasUserPermission(
        IUser $user,
        mixed $permissionName
    ): bool;

    /**
     * Return if this is a valid crud route.
     */
    public function isValidCrudRoute(Route $route): bool;

    /**
     * Get resource provider associated with this route.
     *
     *
     * @return ICrudResourceProvider
     */
    public function getProviderFromRoute(Route $route): ?ICrudResourceProvider;

    /**
     * Get verb associated with this route.
     *
     *
     * @return ICrudVerb
     */
    public function getVerbFromRoute(Route $route): ?ICrudVerb;

    /**
     * Get resource provider class associated with this route.
     */
    public function getProviderClassFromRoute(Route $route): ?string;

    /**
     * Get verb class associated with this route.
     */
    public function getVerbClassFromRoute(Route $route): ?string;
}
