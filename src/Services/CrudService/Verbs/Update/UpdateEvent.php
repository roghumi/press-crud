<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Update;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Roghumi\Press\Crud\Helpers\UserHelpers;
use Roghumi\Press\Crud\Services\AccessService\IUser;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;

class UpdateEvent implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     *
     * @return UpdateEvent
     */
    public function __construct(
        public int|string $userId,
        public string $providerClass,
        public int|string $modelId,
        public int $timestamp
    ) {
        //
    }

    /**
     * Get modified resource crud provider.
     */
    public function getCrudProvider(): ICrudResourceProvider
    {
        $class = $this->providerClass;

        return new $class();
    }

    /**
     * Get modified crud resource model.
     */
    public function getObjectModel(): Model
    {
        return $this
            ->getCrudProvider()
            ->getObjectById($this->modelId)
            ->getModel();
    }

    /**
     * Get user that dispatched this crud event.
     */
    public function getDispatcher(): IUser
    {
        return UserHelpers::getUserWithId($this->userId);
    }
}
