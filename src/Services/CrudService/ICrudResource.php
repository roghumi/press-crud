<?php

namespace Roghumi\Press\Crud\Services\CrudService;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Crud Resource interface.
 * Just used as a contract for getting a Model from resource.
 */
interface ICrudResource
{
    /**
     * get eloquent object for this resource item
     *
     * @return Model
     */
    public function getModel(): Model;
}
