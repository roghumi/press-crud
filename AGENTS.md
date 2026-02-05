# AGENTS.md - LaraPress Crud Development Guide

## Project Overview
This is a Laravel package for creating REST APIs with CRUD operations and role-based access control (RBAC). It provides easy REST API endpoints for Eloquent models with advanced querying capabilities and security features.

## Build/Lint/Test Commands

### Running Tests
- Run all tests: `docker compose run -it phpcli phpunit`
- Run specific test class: `docker compose run -it phpcli phpunit tests/Unit/YourTestClass.php`
- Run specific test method: `docker compose run -it phpcli phpunit tests/Unit/YourTestClass.php --filter testMethodName`
- Run tests with coverage: `docker compose run -it phpcli phpunit --coverage-html .coverage/html`

### Linting and Code Style
- Check Laravel pint (PHP code style): `docker compose run -it phpcli pint --test`
- Fix Laravel pint issues: `docker compose run -it phpcli pint`
- Check with phpcs: `docker compose run -it phpcli phpcs`
- Fix phpcs issues: `docker compose run -it phpcli phpcbf`
- Check phpcs with diff report: `docker compose run -it phpcli phpcs --report=diff`
- Fix issues on dirty (uncommitted) files: `docker compose run -it phpcli pint --dirty`

### Development Environment
- Build development image: `docker compose build phpcli --build-arg UID=$(id -u) --build-arg GID=$(id -g)`
- Install composer packages: `docker compose run -it phpcli composer install`

## Code Style Guidelines

### PHP Standards
- Follow PSR-2 coding standards
- Use PSR-4 autoloading
- Maintain consistent indentation (4 spaces)
- Use Unix line endings

### Naming Conventions
- Use PascalCase for class names
- Use camelCase for methods and variables
- Use snake_case for database columns
- Use descriptive names that indicate purpose

### Imports and Autoloading
- Use fully qualified class names in PHP files
- Import classes with `use` statements at the top of files
- Follow PSR-4 autoloading structure
- All classes in `src/` directory are autoloaded under `Roghumi\Press\Crud\` namespace

### Error Handling
- Use Laravel's exception handling mechanisms
- Implement try-catch blocks appropriately
- Return proper HTTP status codes for errors
- Log errors appropriately using Laravel's logging system

### Documentation
- Use PHPDoc blocks for all classes and methods
- Document parameters, return values, and exceptions
- Follow existing code documentation patterns

### Testing
- Write unit tests for all new features
- Write feature tests for complex interactions
- Maintain test coverage
- Use Laravel's testing helpers

### File Structure
- Source code in `src/` directory
- Unit tests in `tests/Unit/` directory
- Feature tests in `tests/Feature/` directory
- CRUD-specific tests in `tests/Crud/` directory

## Development Setup
1. Clone repository
2. Build Docker development environment
3. Install Composer dependencies
4. Checkout to new branch (dev-{name}, fix-{name}, or next-{name})
5. Run tests to verify setup
6. Lint code before committing
7. Create pull request

## Branch Naming Conventions
- `dev-{feature-name}` for new features
- `fix-{issue-name}` for bug fixes and improvements
- `next-{change-name}` for breaking changes