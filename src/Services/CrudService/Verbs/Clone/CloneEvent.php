<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Clone;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Roghumi\Press\Crud\Helpers\UserHelpers;
use Roghumi\Press\Crud\Services\AccessService\IUser;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;

class CloneEvent implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public int|string $userId,
        public string $providerClass,
        public int|string $sourceId,
        public array $clonedIds,
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
    public function getSourceModel(): Model
    {
        return $this
            ->getCrudProvider()
            ->getObjectById($this->sourceId)
            ->getModel();
    }

    /**
     * Get cloned model ids
     */
    public function getClonedModelIds(): Collection
    {
        return new Collection($this->clonedIds);
    }

    /**
     * Get user that dispatched this crud event.
     */
    public function getDispatcher(): IUser
    {
        return UserHelpers::getUserWithId($this->userId);
    }
}
