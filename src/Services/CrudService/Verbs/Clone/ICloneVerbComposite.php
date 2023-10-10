<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Clone;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerbComposite;

/**
 * Clone verb composite interface. Every resource composite that
 * needs manipulating data before or after duplicating needs to
 * implement this interface.
 */
interface ICloneVerbComposite extends ICrudVerbComposite
{
    /**
     * Called just before duplicating a model. sanitized data in
     * crud verb composite is used to create a model, now one can
     * access this model and make necessary changes before clones
     * are stored in database. return a list of clone ready models
     *
     * @param  Request  $request incoming request.
     * @param  Model  $source source model that is used for duplicating.
     * @param  Collection  $targets Array of target objects created and ready to be stored in database.
     * @param  array  ...$args incoming route args.
     *
     * @throws Exception
     */
    public function onBeforeClone(Request $request, Model $source, Collection $targets, ...$args): void;

    /**
     * Called after a model is successfully cloned in database.
     * ne can use this composite callback to
     * complete a request relation connections and more.
     *
     * @param  Request  $request incoming request.
     * @param  Model  $source source model that is used for duplicating.
     * @param  Collection  $targets Array of target objects created and stored in database.
     * @param  array  ...$args incoming route args.
     *
     * @throws Exception
     */
    public function onAfterClone(Request $request, Model $source, Collection $targets, ...$args): void;
}
