# jotly-laravel
Laravel backend project for jotly

## Run

Copy .env.example to .env, edit it and run `docker-compose up -d`

Once you need generate key for Laravel. Run `docker-compose exec jotly-laravel-laravel.test-1 php artisan key:generate`

## PHPStan (Larastan)

Level: **10**

`./vendor/bin/phpstan analyse` or `make stan`

## PHP_CodeSniffer

Standard: **PSR12**

`./vendor/bin/phpcs /var/www/html/app` or `make lint`

`./vendor/bin/phpcbf /var/www/html/app` or `make fix`

## Swagger

`php artisan swagger:push-documentation` or `make doc` after tests

Or run `make test` to run tests and generate documentation

Swagger UI: http://localhost:8080/doc (or another port from .env)

## Tests

`php artisan test` or `make t`

`make test` to run tests and generate documentation

## Coverage

Actual: **94,66%**

`php artisan test --coverage`
