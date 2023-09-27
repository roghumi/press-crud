<?php

namespace Roghumi\Press\Crud\Resources\Role;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Roghumi\Press\Crud\Facades\RoleService;
use Roghumi\Press\Crud\Services\AccessService\IUser;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Create\ICreateVerbComposite;

class CreateComposite implements ICreateVerbComposite
{
    /**
     * Rules for this verb on resource
     *
     * @param Request $request incoming request
     * @param array $compositeRules Rules from other registered relevant compositions.
     * @param mixed ...$args route args
     *
     * @return array
     */
    public function getRules(Request $request, $compositeRules, ...$args): array
    {
        /** @var IUser */
        $user = Auth::user();

        return array_merge($compositeRules, [
            'name' => 'string|required|max:64|unique:roles,name',
            'options' => 'nullable|min:0|int|max:' . ($user?->getTopRole()?->getOptionsLevel() ?? 0),
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
    }

    /**
     * Sanitized data from request for eloquent model
     *
     * @param Request $request incoming request
     * @param array $data Data from other registered relevant compositions.
     * @param mixed ...$args route args
     *
     * @return array
     */
    public function getSanitized(Request $request, array $data, ...$args): array
    {
        return array_merge($data, [
            'name' => $request->get('name', null),
            'options' => $request->get('options', 0),
            'author_id' => Auth::id(),
        ]);
    }

    /**
     * Called just before creating a model. sanitized data in
     * crud verb composite is used to create a model, now one can
     * Access this model and make necessary changes before model is stored.
     *
     * @param Request $request incoming request
     * @param Model $resource model that will be created
     * @param mixed ...$args incoming route args
     *
     * @throws Exception
     *
     * @return void
     */
    public function onBeforeCreate(Request $request, Model $resource, ...$args): void
    {
    }

    /**
     * Called after a model is successfully created in database.
     * ne can use this composite callback to
     * complete a request relation connections and more.
     *
     * @param Request $request incoming request
     * @param Model $resource newly created model
     * @param mixed ...$args incoming route args
     *
     * @throws Exception
     *
     * @return void
     */
    public function onAfterCreate(Request $request, Model $resource, ...$args): void
    {
        // sync role permissions
        RoleService::syncRolePermissions($resource->id, $request->get('permissions', []));
    }
}
