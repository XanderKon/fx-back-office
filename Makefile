.PHONY: build run full-run down migrate composer stan psalm phpcs lint test

build:
	cd docker/ && if [ ! -f .env ]; then cp .env.example .env; fi && docker compose build

run:
	make build && cd docker/ && docker compose up

full-run:
	make build && cd docker/ && docker compose up -d && cd ../ && make composer && make migrate && \
	cd docker/ && docker compose exec --user=www-data php-fpm bin/console app:import-rate

down:
	cd docker/ && docker compose down

migrate:
	cd docker/ && docker compose exec --user=www-data php-fpm bin/console doctrine:migrations:migrate --no-interaction

composer:
	cd docker/ && docker compose exec --user=www-data php-fpm composer install

stan:
	cd docker/ && docker compose exec --user=www-data php-fpm ./vendor/bin/phpstan analyse --memory-limit=-1 -c phpstan.neon

psalm:
	cd docker/ && docker compose exec --user=www-data php-fpm ./vendor/bin/psalm --show-info=true

phpcs:
	cd docker/ && docker compose exec --user=www-data php-fpm ./vendor/bin/php-cs-fixer fix

lint:
	clear && make stan && make psalm && make phpcs

test:
	cd docker/ && docker compose exec --user=www-data php-fpm php bin/phpunit

.DEFAULT_GO := build
