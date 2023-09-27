<?php

namespace Roghumi\Press\Crud\Services\CrudService;

use Illuminate\Database\Eloquent\Model;

/**
 * Crud Resource Provider interface.
 * Resources can be introduced to crud system by
 * implementing this interface. Each resource has
 * at least one ResourceProvider implemented for it.
 * With this interface we describe what ICrudVerbs does
 * this resource support and which ICrudVerbCompositions will
 * it be going to use for those verbs.
 *
 */
interface ICrudResourceProvider
{
    /**
     * get a unique name for this resource
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Fully qualified model class name for this resource
     *
     * @return string
     */
    public function getModelClass(): string;

    /**
     * Retrieve object by its ID
     *
     * @param  int|string  $id
     *
     * @return ICrudResource|null
     */
    public function getObjectById($id): ICrudResource|null;

    /**
     * Generate a new resource based on the data that can be stored latish
     *
     * @param array $data
     *
     * @return Model|null
     */
    public function generateModelFromData(array $data): Model|null;

    /**
     * Get a map of available verbs (ICrudVerb interface unique names)
     *  to list of that verb compositions for this resource
     *
     * @return array
     */
    public function getAvailableVerbAndCompositions(): array;
}
