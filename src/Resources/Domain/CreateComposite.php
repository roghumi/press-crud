<?php

namespace Roghumi\Press\Crud\Resources\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Roghumi\Press\Crud\Facades\DomainService;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Create\ICreateVerbComposite;

/**
 * Domain creation base composite class.
 */
class CreateComposite implements ICreateVerbComposite
{
    /**
     * Rules for domain creation
     *
     * @param Request $request incoming request
     * @param array $compositeRules Rules from other registered relevant compositions.
     * @param mixed ...$args route args
     *
     * @return array
     */
    public function getRules(Request $request, array $compositeRules, ...$args): array
    {
        return array_merge($compositeRules, [
            'name' => 'string|required|max:64|unique:domains,name',
            'parentId' => 'numeric|nullable|exists:domains,id',
            'data' => 'nullable|json',
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
            'author_id' => Auth::id(),
            'data' => json_decode($request->get('data', null)),
        ]);
    }

    /**
     * Before domain creation
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
     * After domain created:
     *  * update domain position in hierarchy based on request parentId parameter
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
        // update domain position in hierarchy if new parent value is present in request
        $parentId = $request->get('parentId', null);
        if (! is_null($parentId)) {
            DomainService::addDomainAsChild($parentId, $resource->id);
        }
    }
}
