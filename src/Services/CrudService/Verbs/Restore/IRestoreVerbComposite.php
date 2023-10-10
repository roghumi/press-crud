<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Restore;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerbComposite;

/**
 * Restore verb composite interface. Every resource composite that
 * needs manipulating data before or after deletion needs to
 * implement this interface.
 */
interface IRestoreVerbComposite extends ICrudVerbComposite
{
    /**
     * Called just before restoring a model. sanitized data in
     * crud verb composite is used to create a model, now one can
     * Access this model and make necessary changes before model is stored.
     *
     * @param  Request  $request incoming request
     * @param  Model  $resource model that will be deleted
     * @param  array  ...$args incoming route args
     *
     * @throws Exception
     */
    public function onBeforeRestore(Request $request, Model $resource, ...$args): void;

    /**
     * Called after a model is successfully restored in database.
     * ne can use this composite callback to
     * complete a request relation connections and more.
     *
     * @param  Request  $request incoming request
     * @param  Model  $resource model that was deleted
     * @param  array  ...$args incoming route args
     *
     * @throws Exception
     */
    public function onAfterRestore(Request $request, Model $resource, ...$args): void;
}
