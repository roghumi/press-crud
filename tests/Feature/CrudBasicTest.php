<?php

namespace Roghumi\Press\Crud\Tests\Feature;

use Roghumi\Press\Crud\Tests\Helpers\CrudBasicTester;

class CrudBasicTest extends CrudBasicTester
{
    protected function getTablesForTest(): array
    {
        return [
            'permissions' => null,
            'users' => null,
            'roles' => null,
            'permission_role' => null,
            'role_user' => null,
        ];
    }
}
