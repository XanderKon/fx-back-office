## Приложение "Конвертер валют"

Понадобится docker, docker compose

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

- Запуск PHPStan

  `docker compose exec --user=www-data php-fpm vendor/bin/phpstan analyse -c phpstan.neon`

- Запуск PHP CS Fixer

  `docker compose exec --user=www-data php-fpm vendor/bin/php-cs-fixer fix`

- Запуск Psalm

  `docker compose exec --user=www-data php-fpm vendor/bin/psalm`
