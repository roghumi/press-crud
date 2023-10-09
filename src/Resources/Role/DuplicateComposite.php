<?php

namespace Roghumi\Press\Crud\Resources\Role;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Roghumi\Press\Crud\Facades\RoleService;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Duplicate\IDuplicateVerbComposite;

class DuplicateComposite extends UpdateComposite implements IDuplicateVerbComposite
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
        return array_merge(
            $compositeRules,
            array_merge(parent::getRules($request, $compositeRules, ...$args), [
                'name' => 'string|nullable|max:64|unique:roles,name',
            ])
        );
    }

    /**
     * Called just before duplicating a model. sanitized data in
     * crud verb composite is used to create a model, now one can
     * access this model and make necessary changes before duplicates
     * are stored in database. return a list of duplicate ready models
     *
     * @param  Request  $request incoming request.
     * @param  Model  $source source model that is used for duplicating.
     * @param  Collection  $targets Array of target objects created and ready to be stored in database.
     * @param  mixed  ...$args incoming route args.
     *
     * @throws Exception
     */
    public function onBeforeDuplicate(Request $request, Model $source, Collection $targets, ...$args): void
    {
        foreach ($targets as $target) {
            $target->name = Str::random(8) . '-' . $request->get('name', $source->name);
        }
    }

    /**
     * Called after a model is successfully duplicated in database.
     * ne can use this composite callback to
     * complete a request relation connections and more.
     *
     * @param  Request  $request incoming request.
     * @param  Model  $source source model that is used for duplicating.
     * @param  Collection  $targets Array of target objects created and stored in database.
     * @param  mixed  ...$args incoming route args.
     *
     * @throws Exception
     */
    public function onAfterDuplicate(Request $request, Model $source, Collection $targets, ...$args): void
    {
        // sync role permissions for duplicated item
        $sourcePermissionNames = $source->getPermissionNames()->toArray();
        foreach ($targets as $target) {
            RoleService::syncRolePermissions($target->id, $sourcePermissionNames);
        }
    }
}
