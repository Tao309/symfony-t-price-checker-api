<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260819121845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Все миграции в одной с pdo_mysql';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE access_token (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, user_identifier VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_B6A2DD685F37A13B (token), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, original_title VARCHAR(255) DEFAULT NULL, isbn VARCHAR(30) DEFAULT NULL, pages SMALLINT DEFAULT NULL, circulation INT DEFAULT NULL, size VARCHAR(20) DEFAULT NULL, publish_year SMALLINT NOT NULL, livelib_id VARCHAR(255) DEFAULT NULL, goodreads_id VARCHAR(255) DEFAULT NULL, fantlab_id VARCHAR(100) DEFAULT NULL, livelib_rating DOUBLE PRECISION DEFAULT NULL, goodreads_rating DOUBLE PRECISION DEFAULT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, book_author_id INT NOT NULL, binding_type_id INT NOT NULL, publishing_house_id INT DEFAULT NULL, publishing_brand_id INT DEFAULT NULL, book_series_id INT DEFAULT NULL, user_created_id INT NOT NULL, INDEX IDX_CBE5A331E4DBE55D (book_author_id), INDEX IDX_CBE5A331C832C4F1 (binding_type_id), INDEX IDX_CBE5A33167402924 (publishing_house_id), INDEX IDX_CBE5A3314802BC39 (publishing_brand_id), INDEX IDX_CBE5A33140D627DA (book_series_id), INDEX IDX_CBE5A331F987D8A8 (user_created_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Книги\' ');
        $this->addSql('CREATE TABLE book_author (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Автор книги\' ');
        $this->addSql('CREATE TABLE book_binding_type (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Тип переплёта\' ');
        $this->addSql('CREATE TABLE book_publishing_brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Издательский брэнд\' ');
        $this->addSql('CREATE TABLE book_publishing_house (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Издательский дом\' ');
        $this->addSql('CREATE TABLE book_series (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Книжная серия\' ');
        $this->addSql('CREATE TABLE book_user_data (release_date DATETIME DEFAULT NULL, listen_price_value INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, book_id INT NOT NULL, user_created_id INT NOT NULL, INDEX IDX_C5DCA9EC16A2B381 (book_id), INDEX IDX_C5DCA9ECF987D8A8 (user_created_id), UNIQUE INDEX bud_book_user (book_id, user_created_id), PRIMARY KEY (book_id, user_created_id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Пользовательские данные по книгам\' ');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(4) NOT NULL, title VARCHAR(255) NOT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Город\' ');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, shop_product_id VARCHAR(30) NOT NULL, shop_product_code VARCHAR(20) DEFAULT NULL, title VARCHAR(255) NOT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, source_product_id INT DEFAULT NULL, book_id INT DEFAULT NULL, shop_id INT NOT NULL, city_id INT DEFAULT NULL, user_created_id INT NOT NULL, INDEX IDX_D34A04AD3930177E (source_product_id), INDEX IDX_D34A04AD16A2B381 (book_id), INDEX IDX_D34A04AD4D16C4DD (shop_id), INDEX IDX_D34A04AD8BAC62AF (city_id), INDEX IDX_D34A04ADF987D8A8 (user_created_id), UNIQUE INDEX IDX_UNIQUE_SHOP_SHOP_PRODUCT (shop_id, shop_product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Продукт\' ');
        $this->addSql('CREATE TABLE product_price (price INT NOT NULL, date_created DATETIME NOT NULL, date_created_string VARCHAR(50) NOT NULL, product_id INT NOT NULL, user_created_id INT NOT NULL, INDEX IDX_6B9459854584665A (product_id), INDEX IDX_6B945985F987D8A8 (user_created_id), UNIQUE INDEX pp_product_user_date (product_id, user_created_id, date_created_string), PRIMARY KEY (product_id, user_created_id, date_created_string)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Цена товара\' ');
        $this->addSql('CREATE TABLE product_stock (qty INT NOT NULL, date_created DATETIME NOT NULL, log JSON DEFAULT NULL, date_created_string VARCHAR(50) NOT NULL, product_id INT NOT NULL, user_created_id INT NOT NULL, INDEX IDX_EA6A2D3C4584665A (product_id), INDEX IDX_EA6A2D3CF987D8A8 (user_created_id), UNIQUE INDEX ps_product_user_date (product_id, user_created_id, date_created_string), PRIMARY KEY (product_id, user_created_id, date_created_string)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Сток товара\' ');
        $this->addSql('CREATE TABLE product_user_data (available TINYINT NOT NULL, not_available_date_from DATETIME DEFAULT NULL, available_date_from DATETIME DEFAULT NULL, listen_price_value INT DEFAULT NULL, listen_qty_value SMALLINT DEFAULT NULL, release_date DATETIME DEFAULT NULL, is_archive TINYINT NOT NULL, comment LONGTEXT DEFAULT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, product_id INT NOT NULL, user_created_id INT NOT NULL, INDEX IDX_B9D209274584665A (product_id), INDEX IDX_B9D20927F987D8A8 (user_created_id), UNIQUE INDEX pud_product_user (product_id, user_created_id), PRIMARY KEY (product_id, user_created_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, domain VARCHAR(50) NOT NULL, url LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Магазин\' ');
        $this->addSql('CREATE TABLE source_product (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, source_product_type_id INT NOT NULL, user_created_id INT NOT NULL, INDEX IDX_9A95BCC5B28AB928 (source_product_type_id), INDEX IDX_9A95BCC5F987D8A8 (user_created_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Источник товара\' ');
        $this->addSql('CREATE TABLE source_product_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Тип источника товара\' ');
        $this->addSql('CREATE TABLE source_product_user_data (listen_price_value INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, source_product_id INT NOT NULL, user_created_id INT NOT NULL, INDEX IDX_86F07B3C3930177E (source_product_id), INDEX IDX_86F07B3CF987D8A8 (user_created_id), UNIQUE INDEX spud_source_product_user (source_product_id, user_created_id), PRIMARY KEY (source_product_id, user_created_id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Пользовательские данные по источникам товара\' ');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(50) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COMMENT = \'Пользователи\' ');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331E4DBE55D FOREIGN KEY (book_author_id) REFERENCES book_author (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331C832C4F1 FOREIGN KEY (binding_type_id) REFERENCES book_binding_type (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33167402924 FOREIGN KEY (publishing_house_id) REFERENCES book_publishing_house (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3314802BC39 FOREIGN KEY (publishing_brand_id) REFERENCES book_publishing_brand (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33140D627DA FOREIGN KEY (book_series_id) REFERENCES book_series (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331F987D8A8 FOREIGN KEY (user_created_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE book_user_data ADD CONSTRAINT FK_C5DCA9EC16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_user_data ADD CONSTRAINT FK_C5DCA9ECF987D8A8 FOREIGN KEY (user_created_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD3930177E FOREIGN KEY (source_product_id) REFERENCES source_product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF987D8A8 FOREIGN KEY (user_created_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE product_price ADD CONSTRAINT FK_6B9459854584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_price ADD CONSTRAINT FK_6B945985F987D8A8 FOREIGN KEY (user_created_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE product_stock ADD CONSTRAINT FK_EA6A2D3C4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_stock ADD CONSTRAINT FK_EA6A2D3CF987D8A8 FOREIGN KEY (user_created_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE product_user_data ADD CONSTRAINT FK_B9D209274584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_user_data ADD CONSTRAINT FK_B9D20927F987D8A8 FOREIGN KEY (user_created_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE source_product ADD CONSTRAINT FK_9A95BCC5B28AB928 FOREIGN KEY (source_product_type_id) REFERENCES source_product_type (id)');
        $this->addSql('ALTER TABLE source_product ADD CONSTRAINT FK_9A95BCC5F987D8A8 FOREIGN KEY (user_created_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE source_product_user_data ADD CONSTRAINT FK_86F07B3C3930177E FOREIGN KEY (source_product_id) REFERENCES source_product (id)');
        $this->addSql('ALTER TABLE source_product_user_data ADD CONSTRAINT FK_86F07B3CF987D8A8 FOREIGN KEY (user_created_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331E4DBE55D');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331C832C4F1');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33167402924');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3314802BC39');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33140D627DA');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331F987D8A8');
        $this->addSql('ALTER TABLE book_user_data DROP FOREIGN KEY FK_C5DCA9EC16A2B381');
        $this->addSql('ALTER TABLE book_user_data DROP FOREIGN KEY FK_C5DCA9ECF987D8A8');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD3930177E');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD16A2B381');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD4D16C4DD');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD8BAC62AF');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADF987D8A8');
        $this->addSql('ALTER TABLE product_price DROP FOREIGN KEY FK_6B9459854584665A');
        $this->addSql('ALTER TABLE product_price DROP FOREIGN KEY FK_6B945985F987D8A8');
        $this->addSql('ALTER TABLE product_stock DROP FOREIGN KEY FK_EA6A2D3C4584665A');
        $this->addSql('ALTER TABLE product_stock DROP FOREIGN KEY FK_EA6A2D3CF987D8A8');
        $this->addSql('ALTER TABLE product_user_data DROP FOREIGN KEY FK_B9D209274584665A');
        $this->addSql('ALTER TABLE product_user_data DROP FOREIGN KEY FK_B9D20927F987D8A8');
        $this->addSql('ALTER TABLE source_product DROP FOREIGN KEY FK_9A95BCC5B28AB928');
        $this->addSql('ALTER TABLE source_product DROP FOREIGN KEY FK_9A95BCC5F987D8A8');
        $this->addSql('ALTER TABLE source_product_user_data DROP FOREIGN KEY FK_86F07B3C3930177E');
        $this->addSql('ALTER TABLE source_product_user_data DROP FOREIGN KEY FK_86F07B3CF987D8A8');
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_author');
        $this->addSql('DROP TABLE book_binding_type');
        $this->addSql('DROP TABLE book_publishing_brand');
        $this->addSql('DROP TABLE book_publishing_house');
        $this->addSql('DROP TABLE book_series');
        $this->addSql('DROP TABLE book_user_data');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_price');
        $this->addSql('DROP TABLE product_stock');
        $this->addSql('DROP TABLE product_user_data');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE source_product');
        $this->addSql('DROP TABLE source_product_type');
        $this->addSql('DROP TABLE source_product_user_data');
        $this->addSql('DROP TABLE `user`');
    }
}
