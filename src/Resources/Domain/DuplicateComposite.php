<?php

namespace Roghumi\Press\Crud\Resources\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Roghumi\Press\Crud\Facades\DomainService;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Duplicate\IDuplicateVerbComposite;

class DuplicateComposite extends UpdateComposite implements IDuplicateVerbComposite
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
                'name' => 'string|nullable|max:64|unique:domains,name',
            ])
        );
    }

    /**
     * Before duplicating domains, prefix duplicated domains with a
     *  random generated string.
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
            $target->name = Str::random(8) . '.' . $request->get('name', $source->name);
        }
    }

    /**
     * After duplicating, position duplicates on same parent as source domain.
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
        $sourceParentId = DomainService::getFirstParentId($source->id);
        if (! is_null($sourceParentId)) {
            DomainService::addAllDomainAsChild($sourceParentId, $targets->pluck('id')->toArray());
        }
    }
}
