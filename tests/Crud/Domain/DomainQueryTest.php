<?php

namespace Roghumi\Press\Crud\Tests\Crud\Domain;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Resources\Domain\DomainProvider;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Query;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\QueryEvent;
use Roghumi\Press\Crud\Tests\Helpers\CrudAPITestBase;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestInvalidRequest;
use Roghumi\Press\Crud\Tests\Helpers\CrudTestValidRequest;

class DomainQueryTest extends CrudAPITestBase
{
    protected function postSetup()
    {
        $this->be($this->adminUser);
    }

    public function getValidRequests()
    {
        return [
            // min required data for query
            new CrudTestValidRequest(
                'api/domain/query',
                'POST',
                new Request([
                    'columns' => ['id', 'name', 'created_at'],
                    'perPage' => 3,
                    'filters' => [
                        'id.between' => [
                            'start' => 5,
                            'end' => 10,
                        ],
                    ],
                    'relations' => [
                        'author' => [
                            'columns' => ['id', 'email'],
                        ],
                    ],
                ]),
                function () {
                    Domain::factory(20)->create();

                    Event::fake([
                        QueryEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.query.success'));
                    $response->assertJsonIsArray('items');
                    $response->assertJsonPath('total', 6);
                    $response->assertJsonPath('page', 1);
                    $response->assertJsonPath('count', 3);
                    $response->assertJsonPath('items.0.id', 5);
                    $response->assertJsonPath('items.1.id', 6);
                    $response->assertJsonMissingPath('items.1.updated_at');
                    $response->assertJsonMissingPath('items.1.deleted_at');
                    $response->assertJsonMissingPath('items.1.author_id');

                    Event::assertDispatched(QueryEvent::class, function (QueryEvent $event) {
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);

                        return true;
                    });
                },
                null
            ),
            // order desc
            new CrudTestValidRequest(
                'api/domain/query',
                'POST',
                new Request([
                    'perPage' => 3,
                    'filters' => [
                        'id.between' => [
                            'start' => 5,
                            'end' => 10,
                        ],
                    ],
                    'relations' => [
                        'author' => [
                            'columns' => ['id', 'email'],
                        ],
                    ],
                    'sortBy' => [
                        'id' => 'desc',
                    ],
                ]),
                function () {
                    Domain::factory(20)->create();

                    Event::fake([
                        QueryEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.query.success'));
                    $response->assertJsonIsArray('items');
                    $response->assertJsonPath('total', 6);
                    $response->assertJsonPath('page', 1);
                    $response->assertJsonPath('count', 3);
                    $response->assertJsonPath('items.0.id', 10);
                    $response->assertJsonPath('items.1.id', 9);

                    Event::assertDispatched(QueryEvent::class, function (QueryEvent $event) {
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);

                        return true;
                    });
                },
                null
            ),
            // column equals
            new CrudTestValidRequest(
                'api/domain/query',
                'POST',
                new Request([
                    'perPage' => 3,
                    'filters' => [
                        'id.equals' => [
                            'operator' => '=',
                            'value' => 4,
                        ],
                    ],
                    'relations' => [
                        'author' => [
                            'columns' => ['id', 'email'],
                        ],
                    ],
                ]),
                function () {
                    Domain::factory(20)->create();

                    Event::fake([
                        QueryEvent::class,
                    ]);
                },
                function (TestResponse $response) {
                    $response->assertStatus(200);
                    $response->assertJsonIsObject();
                    $response->assertJsonPath('message', trans('press.crud.verbs.query.success'));
                    $response->assertJsonIsArray('items');
                    $response->assertJsonPath('total', 1);
                    $response->assertJsonPath('page', 1);
                    $response->assertJsonPath('count', 1);

                    Event::assertDispatched(QueryEvent::class, function (QueryEvent $event) {
                        $this->assertEquals($event->getCrudProvider()::class, DomainProvider::class);

                        return true;
                    });
                },
                null
            ),
        ];
    }

    public function getInvalidRequests()
    {
        return [
            new CrudTestInvalidRequest(
                'api/domain/query',
                'POST',
                new Request([
                    'filters' => [
                        'not_existing' => 'something',
                    ],
                ]),
                function () {
                },
                function () {
                },
                ValidationException::class,
                null
            ),
        ];
    }

    /**
     * @group Domain
     * @group CrudQuery
     */
    public function test_domain_query_success()
    {
        $this->performValidRequestsTests(
            new Query(),
            new DomainProvider(),
            $this->getValidRequests()
        );
    }

    /**
     * @group Domain
     * @group CrudQuery
     */
    public function test_domain_query_invalid_requests()
    {
        $this->performInvalidRequestsTests(
            new Query(),
            new DomainProvider(),
            $this->getInvalidRequests()
        );
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiQuery
     */
    public function test_domain_query_valid_api()
    {
        $this->performValidApiRequestsTests($this->getValidRequests());
    }

    /**
     * @group Domain
     * @group Api
     * @group ApiQuery
     */
    public function test_domain_query_invalid_api()
    {
        $this->performInvalidApiRequestsTests($this->getInvalidRequests());
    }
}
