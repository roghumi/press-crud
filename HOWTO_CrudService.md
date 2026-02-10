# LaraPress CrudService Documentation

## What is CRUD
CRUD stands for Create, Read, Update, and Delete. It's a fundamental concept in database management that represents the four basic operations used to persist data in a database. In web applications, these operations are typically exposed through RESTful APIs to allow clients to interact with server-side data.

## What is LaraPress CRUD
LaraPress CRUD is a Laravel package that provides easy REST API endpoints for Eloquent models with CRUD operations and role-based access control (RBAC). It simplifies the process of creating consistent and secure APIs for your Laravel applications while providing advanced querying capabilities, security features, and a modular architecture for custom functionality.

## How LaraPress CrudService Pipeline Works

The LaraPress CrudService pipeline is a modular system that provides REST API endpoints for Eloquent models with role-based access control. It follows a structured approach to handle CRUD operations:

### Core Services
1. **CrudService**: The main orchestrator that registers routes and executes verbs
2. **AccessService**: Handles role-based access control and permission checking
3. **RoleService**: Manages roles and user role assignments
4. **DomainService**: Provides domain and group based access control extensions

### Pipeline Flow
1. **Route Registration**: Resource providers define available verbs and register appropriate routes
2. **Request Processing**: Middleware validates requests and checks permissions
3. **Verb Execution**: Appropriate verb handler processes the request with validations
4. **Data Transformation**: Verb compositions customize behavior for specific resources
5. **Response Output**: Results returned through standardized response format

### Service Components

#### CRUD Verb Definitions
LaraPress supports the following built-in verbs:
- **Create** - Handles resource creation operations
- **Query** - Handles resource listing and querying with advanced filtering, sorting and relations
- **Update** - Handles resource update operations
- **Delete** - Handles resource deletion operations
- **Restore** - Handles soft-deleted resource restoration (for models with soft deletes)
- **Clone** - Handles resource duplication operations
- **Export** - Handles resource data export operations
Custom new verbs can be added to the pipeline.
// @todo: show how to add a new verb

#### Verb Compositions
Verb compositions are interfaces that define how verbs interact with specific resources. They allow for customization without reimplementing entire verbs:
- **ICreateVerbComposite** - Customizing create verb behavior
- **IUpdateVerbComposite** - Customizing update verb behavior
- **IQueryVerbComposite** - Customizing query verb behavior
- **IDeleteVerbComposite** - Customizing delete verb behavior
- **IRestoreVerbComposite** - Customizing restore verb behavior
- **ICloneVerbComposite** - Customizing clone verb behavior

Each composite defines methods for:
- Defining available columns/fields for queries
- Defining available relations for eager loading
- Defining available filters for querying
- Customizing verb execution flow
- Controlling access to specific operations

#### Resource Providers
Resource providers are classes that define how specific Eloquent models are exposed through the CRUD API. Each provider:
- Defines the model class being exposed
- Specifies the available verbs for that resource
- Defines access control rules for each verb
- Configures verb compositions to customize behavior

#### Access Control
The package provides comprehensive access control through:
- **Role-Based Access Control (RBAC)**: Verb-specific authorization for each role
- **Domain-Based Access Control**: Hierarchical access control structure with groups
- **Permission Management**: Automatic permission generation and validation
- **Middleware Integration**: RoleBasedAccessControl middleware for route protection

### How the Pipeline Works

1. **Initialization**: The CrudService registers routes for each enabled verb and resource provider
2. **Request Routing**: HTTP requests are directed to appropriate verb handlers via registered routes
3. **Authorization**: The RoleBasedAccessControl middleware validates user permissions
4. **Request Processing**: Verbs process requests with composition-based customizations
5. **Data Handling**: Queries are built with filtering, sorting, and relation loading
6. **Response Generation**: Results are returned through standardized output format

### Configuration Example
```php
// Define a resource provider
class UserProvider implements ICrudResourceProvider
{
    public function getName(): string
    {
        return 'user';
    }
    
    public function getModelClass(): string
    {
        return User::class;
    }
    
    public function getAvailableVerbAndCompositions(): array
    {
        return [
            'query' => [new UserQueryComposite()],
            'create' => [new UserCreateComposite()],
            'update' => [new UserUpdateComposite()],
            'delete' => [new UserDeleteComposite()],
        ];
    }
}
```

// @todo: show UserQueryComposite implementation example


### Usage with Middleware
All CRUD routes are automatically protected with RBAC middleware while registered with `crud.php` config file.

## Advanced Features
- JSON querying endpoints with advanced filtering, relation loading and sorting with RBAC
- Modular architecture with interfaces to implement new custom verbs
- Compositions set of interfaces for customizing verb actions (rules, outputs, logics)
- Extending and limiting RBAC with domains and groups
- API for role, domain and group management
- Soft delete support with restore verb for recovering soft-deleted resources
- Export functionality for data export in various formats (CSV, etc.)
- Highly configurable through compositions and providers
