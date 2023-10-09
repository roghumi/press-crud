<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Update;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerbComposite;

/**
 * Update verb composite interface. Every resource that
 * needs manipulating data before or after updating needs to
 * implement this interface.
 */
interface IUpdateVerbComposite extends ICrudVerbComposite
{
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
    public function onBeforeUpdate(Request $request, Model $resource, ...$args): void;

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
    public function onAfterUpdate(Request $request, Model $resource, ...$args): void;
}
