<?php

namespace Roghumi\Press\Crud\Helpers;

use Generator;
use Illuminate\Support\Str;

trait MigrationPublishTrait
{
    /**
     * Searches migrations and publishes them as assets.
     */
    protected function publishMigrations(string $directory): void
    {
        if ($this->app->runningInConsole()) {
            $generator = function (string $directory): Generator {
                foreach ($this->app->make('files')->allFiles($directory) as $file) {
                    yield $file->getPathname() => $this->app->databasePath(
                        'migrations/' . now()->format('Y_m_d_Hi') . Str::after($file->getFilename(), '00_00_00_0000')
                    );
                }
            };

            $this->publishes(iterator_to_array($generator($directory)), 'migrations');
        }
    }
}