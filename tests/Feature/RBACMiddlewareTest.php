<?php

namespace Roghumi\Press\Crud\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Validation\UnauthorizedException;
use Roghumi\Press\Crud\Exceptions\AccessDeniedException;
use Roghumi\Press\Crud\Exceptions\NotACrudRouteException;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Tests\Mock\User\User;
use Roghumi\Press\Crud\Tests\TestCase;

class RBACMiddlewareTest extends TestCase
{
    /**
     * @group Api
     */
    public function test_sample_api()
    {
        $this->assertThrows(
            function () {
                $this->withoutExceptionHandling()
                    ->post(config('press.crud.prefix').'/domain/query', [
                        'rc' => 100,
                    ]);
            },
            UnauthorizedException::class
        );

        User::factory(5)->create();
        Domain::factory(10)->create();

        $this->actingAs(User::find(1))->post(config('press.crud.prefix').'/domain/query', [
            'rc' => 100,
        ])
            ->assertStatus(200)
            ->assertJsonPath('message', trans('press.crud.verbs.query.success', [':count', 10]))
            ->assertJsonIsArray('items')
            ->assertJsonPath('rc', 100)
            ->assertJsonCount(10, 'items');

        $this->actingAs(User::find(1))->post(config('press.crud.prefix').'/domain/query', [
            'perPage' => 5,
            'rc' => 101,
        ])
            ->assertStatus(200)
            ->assertJsonPath('message', trans('press.crud.verbs.query.success', [':count', 5]))
            ->assertJsonIsArray('items')
            ->assertJsonPath('rc', 101)
            ->assertJsonCount(5, 'items');

        $this->assertThrows(
            function () {
                $this->withoutExceptionHandling()
                    ->actingAs(User::find(2))->post(config('press.crud.prefix').'/domain/query', [
                        'perPage' => 5,
                        'rc' => 101,
                    ]);
            },
            AccessDeniedException::class
        );
    }

    public function test_invalid_route()
    {
        Route::middleware(config('press.crud.middleware'))
            ->prefix(config('press.crud.prefix'))
            ->post('/invalid-endpoint', function () {
                return 10;
            });

        $this->assertThrows(
            function () {
                $this->withoutExceptionHandling()
                    ->actingAs(User::find(1))
                    ->post(config('press.crud.prefix').'/invalid-endpoint', []);
            },
            NotACrudRouteException::class
        );
    }
}
