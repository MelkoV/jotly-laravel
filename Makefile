.DEFAULT_GOAL := t

t:
	php artisan test

t-local:
	powershell -Command "$$env:DB_HOST='localhost'; php artisan test"

stan:
	./vendor/bin/phpstan analyse

lint:
	./vendor/bin/phpcs --standard=PSR12 /var/www/html/app

fix:
	./vendor/bin/phpcbf --standard=PSR12 /var/www/html/app

doc:
	php artisan swagger:push-documentation

mutate:
	./vendor/bin/infection

test: t doc

check: stan lint

ci: t stan lint
