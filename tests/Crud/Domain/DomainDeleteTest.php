<?php

namespace Roghumi\Press\Crud\Tests\Crud\Domain;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Roghumi\Press\Crud\Exceptions\ResourceNotFoundException;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Resources\Domain\DomainProvider;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Delete\Delete;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Delete\DeleteEvent;
use Roghumi\Press\Crud\Tests\Helpers\CrudAPITestBase;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestInvalidRequest;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestValidRequest;

class DomainDeleteTest extends CrudAPITestBase
{
    protected function postSetup()
    {
        $this->be($this->adminUser);
    }

    public function getValidRequests()
    {
        return [
            // delete #1
            new CrudTestValidRequest(
                '/api/domain/1',
                'DELETE',
                new Request([
                ]),
                function () {
                    Domain::factory(10)->create();
                    Event::fake([
                        DeleteEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.delete.success'));
                    $response->assertJsonPath('object.id', 1);

                    $target = Domain::withTrashed()->find(1);
                    $this->assertTrue(! is_null($target->deleted_at));

                    Event::assertDispatched(DeleteEvent::class, function (DeleteEvent $event) {
                        $this->assertInstanceOf(Domain::class, $event->getObjectModel());
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);
                        $this->assertEquals(1, $event->modelId);

                        return true;
                    });
                },
                1
            ),
            // delete #5
            new CrudTestValidRequest(
                '/api/domain/5',
                'DELETE',
                new Request([
                ]),
                function () {
                    Event::fake([
                        DeleteEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.delete.success'));
                    $response->assertJsonPath('object.id', 5);

                    $target = Domain::withTrashed()->find(5);
                    $this->assertTrue(! is_null($target->deleted_at));

                    Event::assertDispatched(DeleteEvent::class, function (DeleteEvent $event) {
                        $this->assertInstanceOf(Domain::class, $event->getObjectModel());
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);
                        $this->assertEquals(5, $event->modelId);

                        return true;
                    });
                },
                5
            ),
        ];
    }

    public function getInvalidRequests()
    {
        return [
            // invalid domain delete, id does not exist
            new CrudTestInvalidRequest(
                'api/domain/33',
                'DELETE',
                new Request([]),
                function () {
                },
                function ($test) {

                },
                ResourceNotFoundException::class,
                33,
            ),
        ];
    }

    /**
     * @group Domain
     * @group CrudDelete
     */
    public function test_domain_delete_success()
    {
        $this->performValidRequestsTests(
            new Delete(),
            new DomainProvider(),
            $this->getValidRequests()
        );
    }

    /**
     * @group Domain
     * @group CrudDelete
     */
    public function test_domain_delete_invalid_requests()
    {
        $this->performInvalidRequestsTests(
            new Delete(),
            new DomainProvider(),
            $this->getInvalidRequests()
        );
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiDelete
     */
    public function test_domain_delete_valid_api()
    {
        $this->performValidApiRequestsTests($this->getValidRequests());
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiDelete
     */
    public function test_domain_delete_invalid_api()
    {
        $this->performInvalidApiRequestsTests($this->getInvalidRequests());
    }
}
