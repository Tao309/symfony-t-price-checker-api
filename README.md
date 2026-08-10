# symfony-t-price-checker-api 

Реализация функционала плагина для мониторинга цен на маркетплейсах.

Первая (самописная) версия api: https://github.com/Tao309/t-price-checker-api


## Сущности:

- **Book (книга)**
  - **GET** `/api/v1/books/{id}` (Получить книгу)
  - **GET** `/api/v1/books` (Получить список книг)
  - **GET** `/api/v1/books/search/{title}` (Найти книги по названию)
  - **PATCH** `/api/v1/books/{id}` (Обновить книгу)
  - **POST** `/api/v1/books` (Создать книгу)
  - **POST** `/api/v1/books/link/{productId}/{bookId}` (Привязать книгу к продукту)
  - **POST** `/api/v1/books/unlink/{productId}` (Отвязать книгу от продукта)
  

- **BookAuthor (автор книги)**
  - **GET** `/api/v1/book_authors/{id}` (Получить автора книги)
  

- **Product (товар)**
  - **GET** `/api/v1/products/{id}` (Получить товар)
  - **GET** `/api/v1/products?ids=406256148,91138231,...` (Получить список товаров)
  - **PATCH** `/api/v1/products/{id}` (Обновить товар)
  - **POST** `/api/v1/products` (Создать товар)


- **SourceProduct (источник товара)**-
  - **GET** `/api/v1/source_products/{id}` (Получить источник товара)
  - **GET** `/api/v1/source_products/search/{title}` (Найти источники товаров по названию)
  - **PATCH** `/api/v1/source_products/{id}` (Обновить источник товара)
  - **POST** `/api/v1/source_products` (Создать источник товара)
  - **POST** `/api/v1/source_products/link/{productId}/{sourceProductId}` (Привязать источник товара к продукту)
  - **POST** `/api/v1/source_products/unlink/{productId}` (Отвязать источник товара от продукта)


- **BookPublishingHouse (издательский дом)**
  - **POST** `/api/v1/book_publishing_houses` (Создать издательский дом)
  - **PATCH** `/api/v1/book_publishing_houses/{id}` (Обновить издательский дом)


- **BookPublishingBrand (издательский брэнд)**
  - **POST** `/api/v1/book_publishing_houses` (Создать издательский брэнд)
  - **PATCH** `/api/v1/book_publishing_houses/{id}` (Обновить издательский брэнд)


- **BookSeries (книжная серия)**
  - **POST** `/api/v1/book_series` (Создать книжную серию)
  - **PATCH** `/api/v1/book_series/{id}` (Обновить книжную серию)


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
- Импорт пользовательских данных по книгам `php bin/console import:book_user_data`
- Импорт источников товаров `php bin/console import:source_products`
- Импорт пользовательских данных по источникам товаров `php bin/console import:source_product_user_data`
- Импорт пользовательских данных по товарам `php bin/console import:product_user_data`

Доступные опции:
- `show-parsing-log` Показывать детали парсинга (по умолчанию да)
- `fake` Фейковый запрос, без записи в БД (по умолчанию да)
- `just-check-parsing` Проверить только парсинг, без создания новых сущностей (по умолчанию да)



## Запуск проекта локально:
- `docker-compose up --build -d`
- переходим в bash контейнера php
- `composer install`
- `php bin/console d:m:m`
- `php bin/console doctrine:fixtures:load`
- `php bin/console import:run-all --fake=0 --just-check-parsing=0`


## Реализовать:

- реализовать CI/CD для проекта
- добавить redis. Хранить: магазины, конфиги в response
- реализовать апи методы v1 на модели через api-platform, со всеми связями, фильтрами по пользователю, магазину
- добавить ограничения на апи ресурсы
- добавить https://api-platform.com/docs/symfony/jwt/
- добавить роли пользователей
- добавить права на пользователей по доступу к функционалу (поле access_rights в конфигах)
- добавить лимит при выводе продуктов (после прав пользователей)
- конфиг вынести в отдельный метод апи? Или в openapi добавить config
- лимит по умолчанию для запросов общих в GetCollection
- описать Exception-ы
- добавить в response: success, message, data, trace и т.д. 
- при получении продукта учитывать shop_type, чтобы не было возможности получить инфу по продукты с озона, если 
  отправлю тип = вб
- разобрать сохранение даты с 2026-08-06T12:30:28.000Z
- написать юнит-тесты