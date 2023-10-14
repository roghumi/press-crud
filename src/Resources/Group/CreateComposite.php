<?php

namespace Roghumi\Press\Crud\Resources\Group;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Create\ICreateVerbComposite;
use Roghumi\Press\Crud\Helpers\UserHelpers;
use Illuminate\Validation\Rule;

/**
 * Group creation base composite class.
 * Define rules, sanitizations and before, after create hooks.
 */
class CreateComposite implements ICreateVerbComposite
{
    /**
     * Rules for Group creation
     *
     * @param  Request  $request incoming request
     * @param  array  $compositeRules Rules from other registered relevant compositions.
     * @param  mixed  ...$args route args
     */
    public function getRules(Request $request, array $compositeRules, ...$args): array
    {
        return array_merge($compositeRules, [
            'name' => ['string', 'required', Rule::unique('groups', 'name')],
'options' => 'numeric|nullable|min:0'
        ]);
    }

    /**
     * Sanitized data from request for eloquent model
     *
     * @param  Request  $request incoming request
     * @param  array  $data Data from other registered relevant compositions.
     * @param  mixed  ...$args route args
     */
    public function getSanitized(Request $request, array $data, ...$args): array
    {
        return array_merge($data, [
            'name' => $request->get('name', null),
'options' => $request->get('options', 0)
        ]);
    }

    /**
     * Callback hook before resource is created
     *
     * @param  Request  $request incoming request
     * @param  Model  $resource model that will be created
     * @param  mixed  ...$args incoming route args
     *
     * @throws Exception
     */
    public function onBeforeCreate(Request $request, Model $resource, ...$args): void
    {
    }

    /**
     * Callback hook after resource created
     *
     * @param  Request  $request incoming request
     * @param  Model  $resource newly created model
     * @param  mixed  ...$args incoming route args
     *
     * @throws Exception
     */
    public function onAfterCreate(Request $request, Model $resource, ...$args): void
    {
    }
}
