<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Duplicate;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;

class DuplicateEvent implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Duplicate new event instance.
     *
     * @param string|int $userId
     * @param string $providerClass
     * @param string|int $sourceId
     * @param array $duplicateIds
     * @param int $timestamp
     *
     * @return DeleteEvent
     */
    public function __construct(
        public int|string $userId,
        public string $providerClass,
        public int|string $sourceId,
        public array $duplicateIds,
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
    public function getSourceModel(): Model
    {
        return $this
            ->getCrudProvider()
            ->getObjectById($this->sourceId)
            ->getModel();
    }

    /**
     * @return Collection
     */
    public function getDuplicatedModelIds(): Collection
    {
        return new Collection($this->duplicateIds);
    }
}
