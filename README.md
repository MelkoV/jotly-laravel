# jotly-laravel
Laravel backend project for jotly

## Run

Copy .env.example to .env, edit it and run `docker-compose up -d`

Once you need generate key for Laravel. Run `docker-compose exec jotly-laravel-laravel.test-1 php artisan key:generate`

## PHPStan (Larastan)

Level: 10

`./vendor/bin/phpstan analyse`

## PHP_CodeSniffer

Standard: PSR12

`./vendor/bin/phpcs /var/www/html/app`

`./vendor/bin/phpcbf /var/www/html/app`

## Swagger

`php artisan swagger:push-documentation` after tests

Swagger UI: http://localhost:8080/doc (or another port from .env)
