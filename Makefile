.DEFAULT_GOAL := test

test:
	php artisan test

stan:
	./vendor/bin/phpstan analyse

sniffer:
	./vendor/bin/phpcs /var/www/html/app

fixer:
	./vendor/bin/phpcbf /var/www/html/app
