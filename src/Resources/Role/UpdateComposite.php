<?php

namespace Roghumi\Press\Crud\Resources\Role;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Roghumi\Press\Crud\Facades\RoleService;
use Roghumi\Press\Crud\Services\AccessService\IUser;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Update\IUpdateVerbComposite;

class UpdateComposite implements IUpdateVerbComposite
{
    /**
     * Rules for this verb on resource
     *
     * @param  Request  $request incoming request
     * @param  array  $compositeRules composition rules before this composite
     * @param  mixed  ...$args incoming route args
     */
    public function getRules(Request $request, $compositeRules, ...$args): array
    {
        /** @var IUser */
        $user = Auth::user();

        return array_merge($compositeRules, [
            'name' => ['string', 'max:64', Rule::unique(Domain::class)->ignore($args[0])],
            'options' => 'nullable|min:0|int|max:' . ($user?->getTopRole()?->getOptionsLevel() ?? 0),
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
    }

    /**
     * Return sanitized data that will be used by eloquent for creating the model.
     *
     * @param  Request  $request incoming request.
     * @param  array  $data composition data chained to this composite.
     * @param  mixed  ...$args incoming route args.
     */
    public function getSanitized(Request $request, array $data, ...$args): array
    {
        if (! is_null($request->get('name', null))) {
            $data['name'] = $request->get('name');
        }

        return $data;
    }

    /**
     * Called just before updating a model. One can
     * access this model and make necessary changes
     * before the model is updated.
     *
     * @param  Request  $request incoming request
     * @param  Model  $resource model that will be created
     * @param  mixed  ...$args incoming route args
     *
     * @throws ValidationException
     * @throws Exception
     */
    public function onBeforeUpdate(Request $request, Model $resource, ...$args): void
    {
    }

    /**
     * Called after a model is successfully updated in database.
     * One can use this composite callback to complete a requests
     * relation connections and more.
     *
     * @param  Request  $request incoming request
     * @param  Model  $resource newly created model
     * @param  mixed  ...$args incoming route args
     *
     * @throws ValidationException
     * @throws Exception
     */
    public function onAfterUpdate(Request $request, Model $resource, ...$args): void
    {
        // sync roles for duplicated item
        RoleService::syncRolePermissions($resource->id, $request->get('permissions', []));
    }
}
