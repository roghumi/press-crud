# How to use

## Setup
1. Install the package via composer:
```
composer require roghumi/larapress-crud
```

1. Publish the configuration file:
```
php artisan vendor:publish --provider="Roghumi\Press\Crud\CrudServiceProvider"
```

1. Run the migrations:
```
php artisan migrate
```

## Available Services:
This package contains a number of base services to handle `RB Access Control` of defined `Resources` for a `CRUD` API.

These services include:
1. AccessService: Manage role based access on `Resources`. Works as a separate pipeline of Laravel's `Permissions` and `Gates`.
1. CrudService: Resource API endpoints and verb execution pipeline.
1. DomainService: Hierarchy based access control over `Resources`
1. RoleService: Manages user roles.
1. Generator: Define `Resources` with yaml syntax and generate appropriate `CrudServices` for them.
