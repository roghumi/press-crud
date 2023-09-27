<?php

namespace Roghumi\Press\Crud\Tests\Helpers;

use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;
use Roghumi\Press\Crud\Facades\CrudService;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;
use Roghumi\Press\Crud\Tests\TestCase;

class CrudAPITestBase extends TestCase
{
    /**
     * assert crud execution of invalid request with crud service directly
     *
     * @return static
     */
    protected function assertCrudVerbInvalidRequest(
        ICrudVerb $verb,
        ICrudResourceProvider $provider,
        Request $request,
        string $exceptionClass,
        ...$args
    ) {
        return $this->assertThrows(
            function () use ($verb, $provider, $request, $args) {
                CrudService::executeVerbForResource(
                    $verb,
                    $provider,
                    $request,
                    ...$args
                );
            },
            $exceptionClass
        );
    }

    /**
     * assert crud execution of valid request with crud service directly
     *
     * @return TestResponse
     */
    protected function assertCrudVerbValidRequest(
        ICrudVerb $verb,
        ICrudResourceProvider $provider,
        Request $request,
        ...$args
    ) {
        return new TestResponse(CrudService::executeVerbForResource(
            $verb,
            $provider,
            $request,
            ...$args
        ));
    }

    /**
     * @return TestResponse
     */
    protected function assertApiVerbValidRequest(
        string $url,
        string $method,
        array $data
    ) {
        $server = $this->transformHeadersToServerVars([
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ]);
        $cookies = $this->prepareCookiesForRequest();

        return $this->call($method, $url, [], $cookies, [], $server, json_encode($data));
    }

    /**
     * @return TestResponse
     */
    protected function assertApiVerbInvalidRequest(
        string $url,
        string $method,
        array $data,
        string $exceptionClass
    ) {
        $server = $this->transformHeadersToServerVars([
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ]);
        $cookies = $this->prepareCookiesForRequest();

        $this->assertThrows(
            function () use ($method, $cookies, $url, $server, $data) {
                $this->withoutExceptionHandling()->call($method, $url, [], $cookies, [], $server, json_encode($data));
            },
            $exceptionClass
        );
    }

    /**
     * Perform a series of valid requests on API endpoint
     */
    protected function performValidApiRequestsTests(
        array $validRequests
    ) {
        /** @var CrudTestValidRequest[] $validRequests */
        foreach ($validRequests as $testRequest) {
            $testRequest->onBeforeExecutionStarts();
            $testRequest->assertTestResponse($this->assertApiVerbValidRequest(
                $testRequest->url,
                $testRequest->method,
                $testRequest->getRequest()->all()
            ));
        }

        return $this;
    }

    /**
     * Perform a series of valid requests on API endpoint
     */
    protected function performInvalidApiRequestsTests(
        array $invalidRequests
    ) {
        /** @var CrudTestInvalidRequest[] $invalidRequests */
        foreach ($invalidRequests as $testRequest) {
            $testRequest->onBeforeExecutionStarts();
            $this->assertApiVerbInvalidRequest(
                $testRequest->url,
                $testRequest->method,
                $testRequest->getRequest()->all(),
                $testRequest->getExpectedExceptionClass()
            );
        }

        return $this;
    }

    /**
     * Perform a series of valid requests both on CrudService
     *
     * @return void
     */
    protected function performValidRequestsTests(
        ICrudVerb $verb,
        ICrudResourceProvider $provider,
        array $validRequests
    ): static {
        /** @var CrudTestValidRequest[] $validRequests */
        foreach ($validRequests as $testRequest) {
            $testRequest->onBeforeExecutionStarts();
            $testRequest->assertTestResponse($this->assertCrudVerbValidRequest(
                $verb,
                $provider,
                $testRequest->getRequest(),
                ...$testRequest->getArgs()
            ));
        }

        return $this;
    }

    /**
     * Perform a series of invalid requests both on CrudService
     */
    protected function performInvalidRequestsTests(
        ICrudVerb $verb,
        ICrudResourceProvider $provider,
        array $invalidRequests
    ): static {
        /** @var CrudTestInvalidRequest[] $invalidRequests */
        foreach ($invalidRequests as $testRequest) {
            $testRequest->onBeforeExecutionStarts();
            $this->assertCrudVerbInvalidRequest(
                $verb,
                $provider,
                $testRequest->getRequest(),
                $testRequest->getExpectedExceptionClass(),
                ...$testRequest->getArgs(),
            );
        }

        return $this;
    }
}
