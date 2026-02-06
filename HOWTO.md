# User guide
## Table of contents
* [Introduction, What is Crud?](#introduction)
* [Crud for Laravel](#crud-for-laravel)
* [LaraPress Crud Verbs](#larapress-crud-verbs)
* [LaraPress Verb compositions](#larapress-verb-compositions)
* [LaraPress Crud Resource Providers](#larapress-crud-resource-providers)
* [Role based access control](#role-based-access-control)
* [Domain based access control](#domain-based-access-control)
* [Installation](#installation)
* [Usage Examples](#usage-examples)
* [Advanced Features](#advanced-features)

### Introduction, What is Crud?
CRUD stands for Create, Read, Update, and Delete. It's a fundamental concept in database management that represents the four basic operations used to persist data in a database. In web applications, these operations are typically exposed through RESTful APIs to allow clients to interact with server-side data.

### Crud for Laravel
LaraPress Crud is a Laravel package that provides easy REST API endpoints for Eloquent models with CRUD operations and role-based access control (RBAC). It simplifies the process of creating consistent and secure APIs for your Laravel applications while providing advanced querying capabilities, security features, and a modular architecture for custom functionality.

### LaraPress Crud Verbs
LaraPress Crud provides a set of standardized verbs that define the available operations on resources. These verbs are the building blocks of the API endpoints exposed by the package.

Available verbs include:
* **Create** - Handles resource creation operations
* **Query** - Handles resource listing and querying with advanced filtering, sorting and relations
* **Update** - Handles resource update operations
* **Delete** - Handles resource deletion operations
* **Restore** - Handles soft-deleted resource restoration (for models with soft deletes)
* **Clone** - Handles resource duplication operations
* **Export** - Handles resource data export operations

Each verb defines how the operation is executed, what endpoints it creates, and how it integrates with access controls.

### LaraPress Verb compositions
Verb compositions are interfaces that define how verbs interact with specific resources. They provide a way to customize verb behavior for particular models without creating entirely new verb implementations.

The package includes several composite interfaces which allow you to customize verb behavior:
* **ICreateVerbComposite** - For customizing create verb behavior
* **IUpdateVerbComposite** - For customizing update verb behavior
* **IQueryVerbComposite** - For customizing query verb behavior
* **IDeleteVerbComposite** - For customizing delete verb behavior
* **IRestoreVerbComposite** - For customizing restore verb behavior
* **ICloneVerbComposite** - For customizing clone verb behavior

Each composite defines methods that allow you to:
- Define available columns/fields for queries
- Define available relations for eager loading
- Define available filters for querying
- Customize verb execution flow
- Control access to specific operations

### LaraPress Crud Resource Providers
Resource providers are classes that define how a specific Eloquent model is exposed through the CRUD API. Each resource provider:
* Defines the model class being exposed
* Specifies the available verbs for that resource
* Defines access control rules for each verb
* Configures verb compositions to customize behavior

Providers implement the ICrudResourceProvider interface and are the central configuration point for each CRUD resource.

### Role based access control
LaraPress Crud implements Role-Based Access Control (RBAC) for securing API endpoints. The package provides:
* Role-based authorization for each verb
* User role management
* Verb-specific access rules
* Integration with Laravel's authentication system

Access control is defined through role providers which determine what roles a user has and what verbs they can execute on specific resources.

### Domain based access control
In addition to role-based access control, the package supports domain-based access control which allows for more granular control over resource access. Domains provide:
* Hierarchical access control structure
* Group-based permissions
* Enhanced security for complex applications
* Integration with user roles

### Installation
To install the LaraPress Crud package, run:

```bash
composer require roghumi/larapress-crud
```

After installation, publish the configuration:

```bash
php artisan vendor:publish --provider="Roghumi\Press\Crud\Providers\PressCrudServiceProvider"
```

### Advanced Features
* **JSON querying endpoints** - Advanced filtering, relation loading and sorting with RBAC
* **Modular architecture** - Interfaces to implement new custom verbs
* **Compositions** - Set of interfaces for customizing verb actions (rules, outputs, logics)
* **Extending and limiting RBAC** - With domains and groups
* **API for role, domain and group management**
* **Soft delete support** - With restore verb for recovering soft-deleted resources
* **Export functionality** - Data export in various formats (CSV, etc.)
* **Highly configurable** - Customizable through compositions and providers
