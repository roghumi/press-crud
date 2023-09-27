<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Delete;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;

class DeleteEvent implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Delete new event instance.
     *
     * @param string|int $userId
     * @param string $providerClass
     * @param string|int $modelId
     * @param int $timestamp
     *
     * @return DeleteEvent
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
        $class = $this->providerClass;

        return new $class();
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
}
