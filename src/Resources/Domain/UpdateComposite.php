<?php

namespace Roghumi\Press\Crud\Resources\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Roghumi\Press\Crud\Facades\DomainService;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Update\IUpdateVerbComposite;

/**
 * Domain general update composite.
 * Checks for name, parentId integrity and
 * updated domains position in hierarchy.
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
            'name' => ['string', 'max:64', Rule::unique(Domain::class)->ignore($args[0])],
            'parentId' => 'numeric|nullable|exists:domains,id',
            'data' => 'nullable|json',
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
        if (! is_null($request->get('name', null))) {
            $compositeData['name'] = $request->get('name');
        }

        if ($request->has('data')) {
            $compositeData['data'] = $request->get('data', null);
        }

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
        // update hierarchy
        if ($request->has('parentId')) {
            $parentId = $request->get('parentId', null);
            if (! is_null($parentId)) {
                DomainService::addDomainAsChild($parentId, $resource->id);
            } else {
                DomainService::removeDomainFromParent($resource->id);
            }
        }
    }
}
