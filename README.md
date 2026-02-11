# jotly-laravel
Laravel backend project for jotly

## Before first run

`docker run --rm --interactive --tty --volume $PWD:/app composer install`

## Run

Copy .env.example to .env, edit it and run `docker-compose up`

Once you need generate key for laravel. Run `docker-compose exec jotly-laravel-laravel.test-1 php artisan key:generate`
