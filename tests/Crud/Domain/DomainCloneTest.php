<?php

namespace Roghumi\Press\Crud\Tests\Crud\Domain;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Roghumi\Press\Crud\Exceptions\ResourceNotFoundException;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Resources\Domain\DomainProvider;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Clone\CloneEvent;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Clone\CloneVerb;
use Roghumi\Press\Crud\Tests\Helpers\CrudAPITestBase;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestInvalidRequest;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestValidRequest;

class DomainCloneTest extends CrudAPITestBase
{
    protected function postSetup()
    {
        $this->be($this->adminUser);
    }

    public function getValidRequests()
    {
        return [
            // clone #1
            new CrudTestValidRequest(
                'api/domain/1/clone',
                'POST',
                new Request([
                ]),
                function () {
                    Domain::factory(1)->create();
                    Event::fake([
                        CloneEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.clone.success'));
                    $response->assertJsonPath('items.0.id', 2);

                    Event::assertDispatched(CloneEvent::class, function (CloneEvent $event) {
                        $this->assertInstanceOf(DomainProvider::class, $event->getCrudProvider());
                        $this->assertEquals($event->getSourceModel()->id, 1);
                        $this->assertEquals($event->getClonedModelIds()->toArray(), [2]);

                        return true;
                    });
                },
                1
            ),
            // clone #2 count 3
            new CrudTestValidRequest(
                'api/domain/2/clone/3',
                'POST',
                new Request([
                ]),
                function () {
                    Event::fake([
                        CloneEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.clone.success'));
                    $response->assertJsonPath('items.0.id', 3);
                    $response->assertJsonPath('items.1.id', 4);
                    $response->assertJsonPath('items.2.id', 5);

                    Event::assertDispatched(CloneEvent::class, function (CloneEvent $event) {
                        $this->assertInstanceOf(DomainProvider::class, $event->getCrudProvider());
                        $this->assertEquals($event->getSourceModel()->id, 2);
                        $this->assertEquals($event->getClonedModelIds()->toArray(), [3, 4, 5]);

                        return true;
                    });
                },
                2,
                3
            ),
        ];
    }

    public function getInvalidRequests()
    {
        return [
            // clone not existing
            new CrudTestInvalidRequest(
                'api/domain/33/clone',
                'POST',
                new Request([]),
                function () {
                },
                function () {
                },
                ResourceNotFoundException::class,
                33
            ),
            // clone with existing name
            new CrudTestInvalidRequest(
                'api/domain/33/clone',
                'POST',
                new Request([
                    'name' => 'example.com',
                ]),
                function () {
                    Domain::factory(5)->create();
                    Domain::factory(1)->create([
                        'name' => 'example.com',
                    ]);
                    Domain::factory(5)->create();
                },
                function () {
                },
                ValidationException::class,
                1
            ),
        ];
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiClone
     */
    public function test_domain_duplicate_success()
    {
        $this->performValidRequestsTests(
            new CloneVerb(),
            new DomainProvider(),
            $this->getValidRequests()
        );
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiClone
     */
    public function test_domain_clone_invalid_requests()
    {
        $this->performInvalidRequestsTests(
            new CloneVerb(),
            new DomainProvider(),
            $this->getInvalidRequests()
        );
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiClone
     */
    public function test_domain_clone_valid_api()
    {
        $this->performValidApiRequestsTests($this->getValidRequests());
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiClone
     */
    public function test_domain_clone_invalid_api()
    {
        $this->performInvalidApiRequestsTests($this->getInvalidRequests());
    }
}
