<?php

namespace Roghumi\Press\Crud\Tests\Feature;

use Roghumi\Press\Crud\Tests\TestCase;

class MakeCompositeTest extends TestCase
{
    /**
     * @group Command
     */
    public function test_make_composite_command()
    {
        $this->artisan('make:composite', [
            'def' => __DIR__.'/../../resources/definitions/group.yml',
            'output-dir' => __DIR__.'/../../src/Resources/Group/',

        ])->assertOk();
    }
}
