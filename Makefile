.PHONY: build run down stan psalm phpcs lint

build:
	cd docker/ && if [ ! -f .env ]; then cp .env.example .env; fi && docker compose build

run:
	make build && cd docker/ && docker compose up

down:
	cd docker/ && docker compose down

stan:
	cd docker/ && docker compose exec --user=www-data php-fpm ./vendor/bin/phpstan analyse --memory-limit=-1 -c phpstan.neon

psalm:
	cd docker/ && docker compose exec --user=www-data php-fpm ./vendor/bin/psalm --show-info=true

phpcs:
	cd docker/ && docker compose exec --user=www-data php-fpm ./vendor/bin/php-cs-fixer fix

lint:
	clear && make stan && make psalm && make phpcs

.DEFAULT_GO := build
