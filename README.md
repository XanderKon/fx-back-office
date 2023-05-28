## Приложение "Конвертер валют"

Понадобится docker, docker compose

В корне проекта лежит Makefile, который предоставляет некий фасад для основных команд.

Чтобы запустить проект:

- Выполняем `make run`, ждём когда образы соберутся и запустятся контейнеры;
- Выполняем `make composer`, затем: http://localhost:8080/.

**ИЛИ**

- Переходим в нужную директорию

  `cd docker`

- Копируем конфигурацию докера и редактируем её согласно потребностям (если необходимо)

  `cp .env.example .env`

- Собираем образы

  `docker compose build`

- Запускаем контейнеры

  `docker compose up`

- Profit!

  http://localhost:8080/

### Полезные команды

- Установка зависимостей (запускать в директории /docker)

   `docker compose exec --user=www-data php-fpm composer install`

- Запуск всех линтеров и анализаторов разом:

  `make lint`

- Запуск PHPStan

  `docker compose exec --user=www-data php-fpm vendor/bin/phpstan analyse -c phpstan.neon`

- Запуск PHP CS Fixer

  `docker compose exec --user=www-data php-fpm vendor/bin/php-cs-fixer fix`

- Запуск Psalm

  `docker compose exec --user=www-data php-fpm vendor/bin/psalm --show-info=true`
