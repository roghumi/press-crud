<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;

class QueryEvent implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     *
     * @return void
     */
    public function __construct(
        public int|string $userId,
        public string $providerClass,
        public int $timestamp
    ) {
        //
    }

    public function getCrudProvider(): ICrudResourceProvider
    {
        $class = $this->providerClass;

        return new $class();
    }
}
