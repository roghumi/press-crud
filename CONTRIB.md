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

## VSCode Extensions to install
* PHP Intelephense
* Laravel Snippets
* Laravel Extra Intellisense


## Development Environment Info
* Docker 23.0.3
* Docker compose 2.17.2
* VSCode 1.77

## PHP Extensions
* XDebug
* Soap
* GD
* CURL
* MBString
