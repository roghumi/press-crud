<?php

namespace Roghumi\Press\Crud\Exceptions;

use Exception;

/**
 * Thrown when an object with hierarchy relation is creating a loop.
 */
class HierarchyLoopException extends Exception
{
}
