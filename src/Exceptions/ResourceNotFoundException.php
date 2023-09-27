<?php

namespace Roghumi\Press\Crud\Exceptions;

use Exception;

/**
 * Exception raised when a resource could not be found when performing a verb action
 */
class ResourceNotFoundException extends Exception
{
    /**
     * Exception for resource id and provider class name
     *
     * @param  mixed  $resourceId
     * @param  string  $providerClass
     */
    public function __construct($resourceId, $providerClass)
    {
        parent::__construct(trans('press.crud.exceptions.resource_not_found', [
            'id' => $resourceId,
            'class' => $providerClass,
        ]));
    }
}
