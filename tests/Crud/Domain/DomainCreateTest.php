<?php

namespace Roghumi\Press\Crud\Tests\Crud\Domain;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Resources\Domain\DomainProvider;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Create\Create;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Create\CreateEvent;
use Roghumi\Press\Crud\Tests\Helpers\CrudAPITestBase;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestInvalidRequest;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestValidRequest;

class DomainCreateTest extends CrudAPITestBase
{
    protected function postSetup()
    {
        $this->be($this->adminUser);
        $this->actingAs($this->adminUser);
    }

    public function getValidRequests(): array
    {
        return [
            // min required data for domain registration
            new CrudTestValidRequest(
                '/api/domain',
                'POST',
                new Request([
                    'name' => 'exampleDomain.com',
                ]),
                function () {
                    Event::fake([
                        CreateEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.create.success'));
                    $response->assertJsonPath('object.name', 'exampleDomain.com');
                    $response->assertJsonPath('object.data', null);

                    Event::assertDispatched(CreateEvent::class, function (CreateEvent $event) {
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);
                        $this->assertInstanceOf(Domain::class, $event->getObjectModel());
                        $this->assertEquals(1, $event->modelId);

                        return true;
                    });
                },
                null
            ),
            // typical domain registration
            new CrudTestValidRequest(
                '/api/domain',
                'POST',
                new Request([
                    'name' => 'secondary.com',
                    'parentId' => 1,
                    'data' => json_encode([
                        'serverIp' => '127.0.0.1',
                        'description' => 'Sample description',
                    ]),
                ]),
                function () {
                    Event::fake([
                        CreateEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.create.success'));
                    $response->assertJsonPath('object.name', 'secondary.com');
                    $response->assertJsonPath('object.data', [
                        'serverIp' => '127.0.0.1',
                        'description' => 'Sample description',
                    ]);

                    Event::assertDispatched(CreateEvent::class, function (CreateEvent $event) {
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);
                        $this->assertInstanceOf(Domain::class, $event->getObjectModel());
                        $this->assertEquals(2, $event->modelId);

                        return true;
                    });
                },
                null
            ),
        ];
    }

    public function getInvalidRequests(): array
    {
        return [
            // invalid domain registration, needs name
            new CrudTestInvalidRequest(
                'api/domain',
                'POST',
                new Request([]),
                function () {
                },
                function ($testInstance) {
                },
                ValidationException::class,
                null
            ),
            // invalid domain registration, parent does not exists
            new CrudTestInvalidRequest(
                'api/domain',
                'POST',
                new Request([
                    'name' => 'invalid.com',
                    'parentId' => 3214, // does not exists
                ]),
                function () {
                },
                function ($testInstance) {
                },
                ValidationException::class,
                null
            ),
        ];
    }

    /**
     * @group Domain
     * @group CrudCreate
     */
    public function test_domain_create_valid_service()
    {
        $this->performValidRequestsTests(
            new Create(),
            new DomainProvider(),
            $this->getValidRequests()
        );
    }

    /**
     * @group Domain
     * @group CrudCreate
     */
    public function test_domain_create_invalid_service()
    {
        $this->performInvalidRequestsTests(
            new Create(),
            new DomainProvider(),
            $this->getInvalidRequests()
        );
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiCreate
     */
    public function test_domain_create_valid_api()
    {
        $this->performValidApiRequestsTests($this->getValidRequests());
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiCreate
     */
    public function test_domain_create_invalid_api()
    {
        $this->performInvalidApiRequestsTests($this->getInvalidRequests());
    }
}
