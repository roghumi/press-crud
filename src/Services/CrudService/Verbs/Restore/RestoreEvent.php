<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Restore;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;

class RestoreEvent implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public $providerClass,
        public $modelId,
        public $timestamp
    ) {
        //
    }

    /**
     * Undocumented function
     */
    public function getCrudProvider(): ICrudResourceProvider
    {
        $class = $this->providerClass;

        return new $class();
    }

    /**
     * Undocumented function
     */
    public function getObjectModel(): Model
    {
        return $this
            ->getCrudProvider()
            ->getObjectById($this->modelId)
            ->getModel();
    }
}
