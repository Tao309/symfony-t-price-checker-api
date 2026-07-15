# symfony-t-price-checker-api 

Реализация функционала плагина для мониторинга цен на маркетплейсах.

Изначальная версия: https://github.com/Tao309/t-price-checker-api


## Сущности:

- **Book (книга)**
  - **GET** `/api/v1/books` (Получить список книг)
  - **POST** `/api/v1/books` (Создать книгу)
  - **GET** `/api/v1/books/{id}` (Получить книгу)
  - **PATCH** `/api/v1/books/{id}` (Обновить книгу)


## Команды:

### Импорт данных с прошлой БД

- Запуск всех команд с импортом `php bin/console import:run-all`
- Импорт серий книг `php bin/console import:book_series`
- Импорт издательских брендов `php bin/console import:book_publishing_brands`
- Импорт издательских домов `php bin/console import:book_publishing_houses`
- Импорт типов источников товаров `php bin/console import:source_product_types`
- Импорт книг `php bin/console import:books` (добавляет и авторов)
- Импорт товаров `php bin/console import:products`
- Импорт цен по товарам `php bin/console import:product_prices`
- Импорт стоков по товарам `php bin/console import:product_stocks`

Доступные опции:
- `show-parsing-log` Показывать детали парсинга (по умолчанию да)
- `fake` Фейковый запрос, без записи в БД (по умолчанию да)
- `just-check-parsing` Проверить только парсинг, без создания новых сущностей (по умолчанию да)



## Запуск проекта локально:
- `docker-compose up --build -d`
- переходим в bash контейнера php
- `composer install`
- `php bin/console doctrine:migration:migrate`
- `php bin/console doctrine:fixtures:load`
- `php bin/console import:run-all --fake=0 --just-check-parsing=0`


## Реализовать:

- добавить модель ProductUserData + импорт
- добавить роли пользователей
- реализовать апи методы v1 на модели через api-platform, со всеми связями
- добавить ограничения в апи ресурсы
- добавить https://api-platform.com/docs/symfony/jwt/
- php-cs-fixer: ширина страницы (код выходит за пределы)