<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Duplicate;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerbComposite;

/**
 * Duplicate verb composite interface. Every resource composite that
 * needs manipulating data before or after duplicating needs to
 * implement this interface.
 */
interface IDuplicateVerbComposite extends ICrudVerbComposite
{
    /**
     * Called just before duplicating a model. sanitized data in
     * crud verb composite is used to create a model, now one can
     * access this model and make necessary changes before duplicates
     * are stored in database. return a list of duplicate ready models
     *
     * @param  Request  $request incoming request.
     * @param  Model  $source source model that is used for duplicating.
     * @param  Collection  $targets Array of target objects created and ready to be stored in database.
     * @param  array  $args incoming route args.
     *
     * @throws Exception
     */
    public function onBeforeDuplicate(Request $request, Model $source, Collection $targets, ...$args): void;

    /**
     * Called after a model is successfully duplicated in database.
     * ne can use this composite callback to
     * complete a request relation connections and more.
     *
     * @param  Request  $request incoming request.
     * @param  Model  $source source model that is used for duplicating.
     * @param  Collection  $targets Array of target objects created and stored in database.
     * @param  array  $args incoming route args.
     *
     * @throws Exception
     */
    public function onAfterDuplicate(Request $request, Model $source, Collection $targets, ...$args): void;
}
