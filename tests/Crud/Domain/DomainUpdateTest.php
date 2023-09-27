<?php

namespace Roghumi\Press\Crud\Tests\Crud\Domain;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Roghumi\Press\Crud\Exceptions\ResourceNotFoundException;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Resources\Domain\DomainProvider;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Update\Update;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Update\UpdateEvent;
use Roghumi\Press\Crud\Tests\Helpers\CrudAPITestBase;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestInvalidRequest;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestValidRequest;

class DomainUpdateTest extends CrudAPITestBase
{
    protected function postSetup()
    {
        $this->be($this->adminUser);
    }

    public function getValidRequests(): array
    {
        return [
            // min required data for domain registration
            new CrudTestValidRequest(
                'api/domain/1/update',
                'POST',
                new Request([
                    'name' => 'updated-example.com',
                ]),
                function () {
                    Domain::factory(1)->create();
                    $this->assertDatabaseCount('domains', 1);

                    Event::fake([
                        UpdateEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.update.success'));
                    $response->assertJsonPath('object.name', 'updated-example.com');

                    Event::assertDispatched(UpdateEvent::class, function (UpdateEvent $event) {
                        $this->assertInstanceOf(Domain::class, $event->getObjectModel());
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);
                        $this->assertEquals(1, $event->modelId);

                        return true;
                    });
                },
                1
            ),
            // same name update on same record should be ok,
            // do not touch other data on update test
            new CrudTestValidRequest(
                'api/domain/2/update',
                'POST',
                new Request([
                    'name' => 'example.com',
                    'options' => 0,
                ]),
                function () {
                    Domain::factory(1)->create([
                        'name' => 'example.com',
                        'data' => json_encode([
                            'desc' => 'sample desc',
                        ]),
                    ]);
                    $this->assertDatabaseCount('domains', 2);

                    Event::fake([
                        UpdateEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.update.success'));
                    $response->assertJsonPath('object.id', 2);
                    $response->assertJsonPath('object.name', 'example.com');
                    $response->assertJsonPath('object.data', json_encode([
                        'desc' => 'sample desc',
                    ]));

                    Event::assertDispatched(UpdateEvent::class, function (UpdateEvent $event) {
                        $this->assertInstanceOf(Domain::class, $event->getObjectModel());
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);
                        $this->assertEquals(2, $event->modelId);

                        return true;
                    });
                },
                2
            ),
        ];
    }

    public function getInvalidRequests(): array
    {
        return [
            // update not existing record
            new CrudTestInvalidRequest(
                'api/domain/34/update',
                'POST',
                new Request([
                    'name' => 'updated-example.com',
                ]),
                function () {
                    Domain::factory(1)->create();
                    $this->assertDatabaseCount('domains', 1);
                },
                function ($response) {
                },
                ResourceNotFoundException::class,
                34
            ),
        ];
    }

    /**
     * @group Domain
     * @group CrudUpdate
     */
    public function test_domain_update_success()
    {
        $this->performValidRequestsTests(
            new Update(),
            new DomainProvider(),
            $this->getValidRequests()
        );
    }

    /**
     * @group Domain
     * @group CrudUpdate
     */
    public function test_domain_update_invalid_requests()
    {
        $this->performInvalidRequestsTests(
            new Update(),
            new DomainProvider(),
            $this->getInvalidRequests()
        );
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiUpdate
     */
    public function test_domain_update_valid_api()
    {
        $this->performValidApiRequestsTests($this->getValidRequests());
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiUpdate
     */
    public function test_domain_update_invalid_api()
    {
        $this->performInvalidApiRequestsTests($this->getInvalidRequests());
    }
}
