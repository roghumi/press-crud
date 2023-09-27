<?php

namespace Roghumi\Press\Crud\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Roghumi\Press\Crud\Http\Middleware\CrossOriginResourceSharing;
use Roghumi\Press\Crud\Tests\TestCase;

class CrossOriginMiddlewareTest extends TestCase
{
    /**
     * @group Service
     */
    public function test_cross_origin_middleware()
    {
        Route::middleware(CrossOriginResourceSharing::class)->match('POST', 'sample-cross-origin', function () {
            return true;
        });

        $this->post('sample-cross-origin')
            ->assertStatus(200)
            ->assertHeader('Access-Control-Allow-Origin', '*')
            ->assertHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
            ->assertHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
    }
}
