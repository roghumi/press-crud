<?php

namespace Roghumi\Press\Crud\Exceptions;

use Exception;

/**
 * Exception raised when verb is not available
 */
class VerbNotFoundException extends Exception
{
    /**
     * Exception for verb name not available
     *
     * @param  string  $verbName
     */
    public function __construct($verbName)
    {
        parent::__construct(trans('press.crud.exceptions.verb_not_found', [
            'verb' => $verbName,
        ]));
    }
}
