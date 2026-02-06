# Contribution Guide

### Run AI Assist:
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

1. Fork the repository
2. Create a new branch for your feature/fix
3. Make your changes
4. Write tests if applicable
5. Submit a pull request

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
