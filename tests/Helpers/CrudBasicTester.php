<?php

namespace Roghumi\Press\Crud\Tests\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Roghumi\Press\Crud\Facades\AccessService;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;
use Roghumi\Press\Crud\Tests\TestCase;

class CrudBasicTester extends TestCase
{
    protected function getTablesForTest(): array
    {
        return [];
    }

    public function test_crud_resources_tables(): void
    {
        foreach ($this->getTablesForTest() as $table => $provider) {
            $this->assertTrue(DB::table($table)->exists(), "Table $table does not exist.");
            if (! is_null($provider)) {
                $this->assertResourceProvider(new $provider());
            }
        }
    }

    /**
     * assert resource provider with all its functionality
     */
    protected function assertResourceProvider(ICrudResourceProvider $provider): void
    {
        $this->assertNotNull($provider->getName());
        $compositionsMap = $provider->getAvailableVerbAndCompositions();
        $availableVerbNames = $this->getAvailableCrudVerbNames();
        $this->assertIsArray($compositionsMap);
        foreach ($compositionsMap as $verb => $compositions) {
            $this->assertTrue(in_array($verb, $availableVerbNames));
            $this->assertIsArray($compositions);
            foreach ($compositions as $composition) {
                $this->assertTrue(class_exists($composition));
            }

            $this->assertCrudVerbForResource($this->getCrudVerbFromName($verb), $provider);
        }
    }

    /**
     * assert verb functionality on resource
     */
    protected function assertCrudVerbForResource(ICrudVerb $verb, ICrudResourceProvider $provider)
    {
        $verbName = $verb->getName();
        $providerName = $provider->getName();

        return $this->assertTrue(Route::has(
            AccessService::getPermissionNameFromVerb($providerName, $verbName)
        ), "Crud route for $verbName on resource $providerName is not registered.");
    }
}
