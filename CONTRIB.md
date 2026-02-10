## Contribution Guid
* Clone this repository
* Create development image: ``docker compose build phpcli --build-arg UID=$(id -u) --build-arg GID=$(id -g)``
* Install composer packages: ``docker compose run -it phpcli composer install``
* Checkout to a new branch: 
    * dev-{some name} for new features
    * fix-{some name} for bug fixes, translations, linting and version updates
    * next-{some name} for breaking changes
* Do your thing
* Run tests
    * ``docker compose run -it phpcli phpunit``
* Lint code
    * ``docker compose run -it phpcli pint``
    * ``docker compose run -it phpcli phpcs``
    * ``docker compose run -it phpcli phpcs --report=diff``
    * ``docker compose run -it phpcli phpcbf``
* Create a pull request

## Run AI Assist:
1. Install Docker & Docker Compose
1. run `docker compose run --rm -it opencode /app`

## Development environment for VSCode setup guid
* ``mkdir .vscode``
* put this as settings.json
```
{
    "LaravelExtraIntellisense.phpCommand": "docker compose exec -it phpcli php -r \"{code}\"",
    "php.validate.executablePath": "dev/php",
    "php.debug.ideKey": "DOCKER",
    "php.debug.executablePath": "dev/php",
    "phpcs.enable": true,
    "phpcs.executablePath": "dev/phpcs",
    "phpcs.autoConfigSearch": false,
    "phpcs.lintOnType": false,
    "phpcs.standard": "Larapress",
    "phpcs.ignorePatterns": [
        "tests/*",
        "lang/*",
        "database/*",
        "routes/*",
        "config/*",
        "vendor/*"
    ]
}
```
* put this as launch.json
```
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/": "${workspaceFolder}"
            },
        }
    ]
}
```

## Code style
This project follows PSR-12 coding standards. Please ensure your code adheres to these standards.

## Testing
All contributions must include appropriate tests. The project uses PHPUnit for testing.

## Reporting issues
Please report any bugs or feature requests in the issue tracker. When reporting bugs, please include:

- A clear description of the problem
- Steps to reproduce
- Expected vs actual behavior
- Any relevant logs or error messages

## Pull Request Guidelines
1. Keep changes focused and small
2. Include comprehensive tests
3. Update documentation as needed
4. Follow the existing code style
5. Write clear commit messages

## License
By contributing to this project, you agree that your contributions will be licensed under the MIT license.
