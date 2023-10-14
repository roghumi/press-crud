<?php

namespace Roghumi\Press\Crud\Resources\Group;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Update\IUpdateVerbComposite;
use Illuminate\Validation\Rule;

/**
 * Group update composite class.
 * Define rules, sanitizations and before, after update hooks.
 */
class UpdateComposite implements IUpdateVerbComposite
{
    /**
     * Rules verified before this verb is executed.
     *
     * @param  Request  $request incoming request
     * @param  array  $compositeRules composition rules before this composite
     * @param  mixed  ...$args incoming route args
     */
    public function getRules(Request $request, $compositeRules, ...$args): array
    {
        return array_merge($compositeRules, [
            'name' => ['string', 'required', Rule::unique('groups', 'name')->ignore($args[0])],
'options' => 'numeric|nullable|min:0'
        ]);
    }

    /**
     * Return sanitized data that will be used by eloquent for creating the model.
     *
     * @param  Request  $request incoming request.
     * @param  array  $compositeData composition data chained to this composite.
     * @param  mixed  ...$args incoming route args.
     * @return void
     */
    public function getSanitized(Request $request, array $compositeData, ...$args): array
    {
        
        return $compositeData;
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
    }
}
