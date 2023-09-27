<?php

namespace Roghumi\Press\Crud\Services\CrudService;

use Illuminate\Http\Request;

/**
 * Most basic crud verb composite interface to implement.
 * Each verb has its own stack of composites, while implementing
 * a resource composite, one should implement all composite interfaces
 * needed for that verb
 */
interface ICrudVerbComposite
{
    /**
     * Rules verified before this verb is executed.
     *
     * @param Request $request incoming request
     * @param array $compositeRules composition rules before this composite
     * @param mixed ...$args incoming route args
     *
     * @return array
     */
    public function getRules(Request $request, array $compositeRules, ...$args): array;

    /**
     * Return sanitized data that will be used by eloquent for creating the model.
     *
     * @param Request $request incoming request.
     * @param array $compositeData composition data chained to this composite.
     * @param mixed ...$args incoming route args.
     *
     * @return array
     */
    public function getSanitized(Request $request, array $compositeData, ...$args): array;
}
