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

### Импорт серий книг
Запуск `php bin/console import:book_series`

Настройки:
- `show-parsing-log` Показывать детали парсинга (по умолчанию да)
- `fake` Фейковый запрос, без записи в БД (по умолчанию да)

Оптимальный запуск команды на проде:
`php bin/console import:book_series --fake=0`

### Импорт издательских брендов
Запуск `php bin/console import:book_publishing_brand`

Настройки:
- `show-parsing-log` Показывать детали парсинга (по умолчанию да)
- `fake` Фейковый запрос, без записи в БД (по умолчанию да)

Оптимальный запуск команды на проде:
`php bin/console import:book_publishing_brand --fake=0`

### Импорт издательских домов
Запуск `php bin/console import:book_publishing_house`

Настройки:
- `show-parsing-log` Показывать детали парсинга (по умолчанию да)
- `fake` Фейковый запрос, без записи в БД (по умолчанию да)

Оптимальный запуск команды на проде:
`php bin/console import:book_publishing_house --fake=0`

### Импорт типов источников товаров
Запуск `php bin/console import:source_product_types`

Настройки:
- `show-parsing-log` Показывать детали парсинга (по умолчанию да)
- `fake` Фейковый запрос, без записи в БД (по умолчанию да)

Оптимальный запуск команды на проде:
`php bin/console import:source_product_types --fake=0`



## Запуск локально:
- `docker-compose up --build -d`
- переходим в bash контейнера php
- `composer install`
- `php bin/console doctrine:migration:migrate`
- `php bin/console doctrine:fixtures:load`


## Реализовать:

- добавить модель BookUserData
- добавить модель ProductUserData
- добавить модель SourceProductUserData
- добавить роли на пользователей
- создать команды для импорта данных с выгрузки БД работающего проекта
- реализовать апи методы v1 на модели
- добавить роли пользователям
- добавить ограничения в апи ресурсы
- добавить https://api-platform.com/docs/symfony/jwt/