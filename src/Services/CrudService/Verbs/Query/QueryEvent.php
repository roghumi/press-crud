<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Roghumi\Press\Crud\Helpers\UserHelpers;
use Roghumi\Press\Crud\Services\AccessService\IUser;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;

class QueryEvent implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     *
     * @return QueryEvent
     */
    public function __construct(
        public int|string $userId,
        public string $providerClass,
        public int $timestamp
    ) {
        //
    }

    /**
     * Get query resource crud provider.
     */
    public function getCrudProvider(): ICrudResourceProvider
    {
        $class = $this->providerClass;

        return new $class();
    }

    /**
     * Get user that dispatched this crud event.
     */
    public function getDispatcher(): IUser
    {
        return UserHelpers::getUserWithId($this->userId);
    }
}
