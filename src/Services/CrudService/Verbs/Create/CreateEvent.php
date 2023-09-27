<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Create;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Roghumi\Press\Crud\Helpers\UserHelpers;
use Roghumi\Press\Crud\Services\AccessService\IUser;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;

class CreateEvent implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;

    /** @var ICrudResourceProvider */
    protected $provider;

    /**
     * Create a new event instance.
     *
     * @param string|int $userId
     * @param string $providerClass
     * @param string|int $modelId
     * @param int $timestamp
     *
     * @return CreateEvent
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
     * @return ICrudResourceProvider
     */
    public function getCrudProvider(): ICrudResourceProvider
    {
        if (is_null($this->provider)) {
            $class = $this->providerClass;
            $this->provider = new $class();
        }

        return $this->provider;
    }

    /**
     * @return Model
     */
    public function getObjectModel(): Model
    {
        return $this
            ->getCrudProvider()
            ->getObjectById($this->modelId)
            ->getModel();
    }

    /**
     * @return IUser
     */
    public function getDispatcher(): IUser
    {
        return UserHelpers::getUserWithId($this->userId);
    }
}
