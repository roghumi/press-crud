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
     * Get modified resource crud provider.
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
