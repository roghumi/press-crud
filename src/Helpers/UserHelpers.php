<?php

namespace Roghumi\Press\Crud\Helpers;

use Illuminate\Support\Facades\Auth;
use Roghumi\Press\Crud\Services\AccessService\IUser;

/**
 * Helper functions for making framework `User` class agnostic.
 */
class UserHelpers
{
    /**
     * retrieve IUser object with $userId as uuid or id.
     */
    public static function getUserWithId($userId): ?IUser
    {
        return call_user_func([config('press.crud.user.class'), 'find'], $userId);
    }

    /**
     * Retrieve authenticated user id if exists, may be id or uuid.
     */
    public static function getAuthUserId(): int|string|null
    {
        return Auth::user()?->id;
    }
}
