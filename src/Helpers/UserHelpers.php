<?php

namespace Roghumi\Press\Crud\Helpers;

use Roghumi\Press\Crud\Services\AccessService\IUser;

class UserHelpers
{
    /**
     * retrieve IUser object with $userId
     *
     * @param  mixed  $userId
     */
    public static function getUserWithId($userId): ?IUser
    {
        return call_user_func([config('press.crud.user.class'), 'find'], $userId);
    }
}
