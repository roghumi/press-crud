# LaraPress Crud
## Easy REST API and access control for eloquent models.

### Features:
* Collection of common API verbs such as Create, Update, Delete, Restore, Duplicate, Query and Export.
* Advanced JSON querying endpoints, with filtering, relation loading and sorting all with RBAC.
* Highly modular and controllable resource definition.
* Interfaces to implement new custom verbs.
* Compositions are a set of interfaces for customizing verb actions (rules, outputs, logics).
* Role based Access control (RBAC) for controller authorization.
* Extending and limiting RBAC with domains and groups.
* API for role, domain and group management. 

### Package Services
This package consists of several core services that work together to provide a comprehensive CRUD solution:

**1. CrudService** 
The main service that orchestrates CRUD operations across resources. It registers routes for CRUD verbs and executes verbs for specific resources. It's the primary entry point for interacting with the package's functionality.

**2. AccessService**
Manages access control for CRUD operations, implementing role-based and domain-based access control. It checks user permissions and enforces authorization rules for each verb and resource.

**3. RoleService** 
Handles role management functionality including creating, assigning, and managing roles for users within the system. Provides the foundation for RBAC implementation.

**4. DomainService**
Manages domain-based access control structures including domains, groups, and hierarchies. Used to extend RBAC with more complex access control requirements.

**5. Command Service**
Includes console commands for generating resource providers and composites, making it easier to implement new CRUD resources in your application.

## Features

### Installation
* `composer require roghumi/larapress-crud`

## Documentation

Please see the [HOWTO.md](HOWTO.md) file for detailed usage instructions.

## Contributing

Please see [CONTRIB.md](CONTRIB.md) for details on contributing to this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
### Author
* Roghumi
    * Email: larapress@roghumi.com
