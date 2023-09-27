<?php

use Illuminate\Support\Facades\Route;
use Roghumi\Press\Crud\Facades\CrudService;

Route::middleware(config('press.crud.middleware'))
    ->prefix(config('press.crud.prefix'))
    ->group(function () {
        // Register crud routes for default resource list.
        CrudService::registerCrudRoutes(config('press.crud.resources'));
    });
