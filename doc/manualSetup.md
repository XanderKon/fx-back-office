### Чтобы запустить проект без использования Makefile:

- Переходим в нужную директорию

  ```bash
  cd docker
  ```

- Копируем конфигурацию докера и редактируем её согласно потребностям (если необходимо)

  ```bash
  cp .env.example .env
  ```

- Собираем образы

  ```bash
  docker compose build
  ```

- Запускаем контейнеры

  ```bash
  docker compose up
  ```

- Устанавливаем зависимости

  ```bash
  docker compose exec --user=www-data php-fpm composer install
  ```

- Запускаем миграции

  ```bash
  docker compose exec --user=www-data php-fpm bin/console doctrine:migrations:migrate --no-interaction
  ```

- Profit!

  http://localhost:8080/
