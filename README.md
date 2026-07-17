# symfony-t-price-checker-api 

Реализация функционала плагина для мониторинга цен на маркетплейсах.

Первая (самописная) версия api: https://github.com/Tao309/t-price-checker-api


## Сущности:

- **Book (книга)**
  - **GET** `/api/v1/books` (Получить список книг)
  - **POST** `/api/v1/books` (Создать книгу)
  - **GET** `/api/v1/books/{id}` (Получить книгу)
  - **PATCH** `/api/v1/books/{id}` (Обновить книгу)
  

- **BookAuthor (автор книги)**
  - **GET** `/api/v1/book_authors/{id}` (Получить автора книги)
  

- **Product (товар)**
  - **GET** `/api/v1/products` (Получить список товаров)
  - **POST** `/api/v1/products` (Создать товар)
  - **GET** `/api/v1/products/{id}` (Получить товар)
  - **PATCH** `/api/v1/products/{id}` (Обновить товар)

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

- добавить роли пользователей
- реализовать апи методы v1 на модели через api-platform, со всеми связями, фильтрами по пользователю, магазину
- добавить ограничения в апи ресурсы
- добавить https://api-platform.com/docs/symfony/jwt/
- реализовать CI/CD для проекта 
- при создании ProductPrice, ProductStock заполнять поле dateString