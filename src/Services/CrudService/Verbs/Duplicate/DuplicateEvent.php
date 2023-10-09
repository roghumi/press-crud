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

    public function getCrudProvider(): ICrudResourceProvider
    {
        $class = $this->providerClass;

        return new $class();
    }

    public function getSourceModel(): Model
    {
        return $this
            ->getCrudProvider()
            ->getObjectById($this->sourceId)
            ->getModel();
    }

    public function getDuplicatedModelIds(): Collection
    {
        return new Collection($this->duplicateIds);
    }
}
