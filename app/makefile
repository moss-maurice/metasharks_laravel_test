.PHONY: init install permissions env key migrate test

install:
	composer install --ignore-platform-reqs

permissions:
	chmod -R 755 storage/

env:
	cp .env.example .env

key:
	php artisan key:generate

migrate:
	php artisan migrate --seed

init: install permissions env key migrate

test:
	php artisan test