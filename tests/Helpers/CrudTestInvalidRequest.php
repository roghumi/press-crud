<?php

namespace Roghumi\Press\Crud\Tests\Helpers;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;

class CrudTestInvalidRequest
{
    protected $args;

    public function __construct(
        public string $url,
        public string $method,
        public Request $request,
        public Closure $onBeforeExecute,
        public Closure $onAfterExecute,
        public string $expectedException,
        ...$args,
    ) {
        $this->args = $args;
    }

    public function getExpectedExceptionClass(): string
    {
        return $this->expectedException;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function onBeforeExecutionStarts()
    {
        $callback = $this->onBeforeExecute;
        if (! is_null($callback)) {
            $callback();
        }
    }

    public function assertTestResponse(TestResponse $response)
    {
        $callback = $this->onAfterExecute;

        return $callback($response);
    }
}
