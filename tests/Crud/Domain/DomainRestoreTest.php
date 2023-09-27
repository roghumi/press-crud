<?php

namespace Roghumi\Press\Crud\Tests\Crud\Domain;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Roghumi\Press\Crud\Exceptions\ResourceNotFoundException;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Resources\Domain\DomainProvider;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Restore\Restore;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Restore\RestoreEvent;
use Roghumi\Press\Crud\Tests\Helpers\CrudAPITestBase;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestInvalidRequest;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestValidRequest;

class DomainRestoreTest extends CrudAPITestBase
{
    protected function postSetup()
    {
        $this->be($this->adminUser);
    }

    public function getValidRequests(): array
    {
        return [
            // delete #1
            new CrudTestValidRequest(
                'api/domain/1/restore',
                'POST',
                new Request([]),
                function () {
                    Domain::factory(10)->create();
                    Domain::find(1)->delete();
                    $target = Domain::withTrashed()->find(1);
                    $this->assertTrue(! is_null($target->deleted_at));

                    Event::fake([
                        RestoreEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.undo_delete.success'));

                    $response->assertJsonPath('object.id', 1);

                    $target = Domain::withTrashed()->find(1);
                    $this->assertTrue(is_null($target->deleted_at));

                    Event::assertDispatched(RestoreEvent::class, function (RestoreEvent $event) {
                        $this->assertInstanceOf(Domain::class, $event->getObjectModel());
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);
                        $this->assertEquals(1, $event->modelId);

                        return true;
                    });
                },
                1
            ),
            // delete #6
            new CrudTestValidRequest(
                'api/domain/6/restore',
                'POST',
                new Request([]),
                function () {
                    Domain::find(6)->delete();
                    $target = Domain::withTrashed()->find(6);
                    $this->assertTrue(! is_null($target->deleted_at));

                    Event::fake([
                        RestoreEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.undo_delete.success'));
                    $response->assertJsonPath('object.id', 6);

                    $target = Domain::withTrashed()->find(6);
                    $this->assertTrue(is_null($target->deleted_at));

                    Event::assertDispatched(RestoreEvent::class, function (RestoreEvent $event) {
                        $this->assertInstanceOf(Domain::class, $event->getObjectModel());
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);
                        $this->assertEquals(6, $event->modelId);

                        return true;
                    });
                },
                6
            ),
        ];
    }

    public function getInvalidRequests(): array
    {
        return [
            // invalid domain delete, id does not exist
            new CrudTestInvalidRequest(
                'api/domain/33/restore',
                'POST',
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
     * @group CrudUndoDelete
     */
    public function test_domain_undo_delete_success()
    {
        $this->performValidRequestsTests(
            new Restore(),
            new DomainProvider(),
            $this->getValidRequests()
        );
    }

    /**
     * @group Domain
     * @group CrudUndoDelete
     */
    public function test_domain_undo_delete_invalid_requests()
    {
        $this->performInvalidRequestsTests(
            new Restore(),
            new DomainProvider(),
            $this->getInvalidRequests()
        );
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiUndoDelete
     */
    public function test_domain_undo_delete_valid_api()
    {
        $this->performValidApiRequestsTests($this->getValidRequests());
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiUndoDelete
     */
    public function test_domain_undo_delete_invalid_api()
    {
        $this->performInvalidApiRequestsTests($this->getInvalidRequests());
    }
}
