<?php

namespace Roghumi\Press\Crud\Exceptions;

use Exception;

/**
 * Thrown when a none crud route is accessed by RBAC middleware.
 */
class NotACrudRouteException extends Exception
{
}
