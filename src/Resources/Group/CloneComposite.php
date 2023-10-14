<?php

namespace Roghumi\Press\Crud\Resources\Group;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Clone\ICloneVerbComposite;

/**
 * Group clone composite class.
 * Define rules, sanitizations and before, after update hooks when cloning this resource.
 */
class CloneComposite extends UpdateComposite implements ICloneVerbComposite
{
    /**
     * Rules for this verb on resource.
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
                'name' => ['string', 'required', Rule::unique('groups', 'name')],
'options' => 'numeric|nullable|min:0'
            ])
        );
    }

    /**
     * Before clone hook
     *
     * @param  Request  $request incoming request.
     * @param  Model  $source source model that is used for cloning.
     * @param  Collection  $targets Array of target objects created and ready to be stored in database.
     * @param  mixed  ...$args incoming route args.
     *
     * @throws Exception
     */
    public function onBeforeClone(Request $request, Model $source, Collection $targets, ...$args): void
    {
    }

    /**
     * After clone hook.
     *
     * @param  Request  $request incoming request.
     * @param  Model  $source source model that is used for cloning.
     * @param  Collection  $targets Array of target objects created and stored in database.
     * @param  mixed  ...$args incoming route args.
     *
     * @throws Exception
     */
    public function onAfterClone(Request $request, Model $source, Collection $targets, ...$args): void
    {
    }
}