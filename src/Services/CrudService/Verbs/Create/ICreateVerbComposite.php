<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Create;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerbComposite;

/**
 * Create verb composite interface. Every resource that
 * needs manipulating data before or after creation needs to
 * implement this interface.
 */
interface ICreateVerbComposite extends ICrudVerbComposite
{
    /**
     * Called just before creating a model. sanitized data in
     * crud verb composite is used to create a model, now one can
     * Access this model and make necessary changes before model is stored.
     *
     * @param Request $request incoming request
     * @param Model $resource model that will be created
     * @param mixed ...$args incoming route args
     *
     * @throws Exception
     *
     * @return void
     */
    public function onBeforeCreate(Request $request, Model $resource, ...$args): void;

    /**
     * Called after a model is successfully created in database.
     * ne can use this composite callback to
     * complete a request relation connections and more.
     *
     * @param Request $request incoming request
     * @param Model $resource newly created model
     * @param mixed ...$args incoming route args
     *
     * @throws Exception
     *
     * @return void
     */
    public function onAfterCreate(Request $request, Model $resource, ...$args): void;
}
