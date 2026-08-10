<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260807060609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '<<<EOF
        1) Перевод полей даты из DateTimeImmutable в DateTime с часовым поясом
        2) Увеличение вместимости поля date_created_string в таблице product_price
        3) Увеличение вместимости поля date_created_string в таблице product_stock
        EOF ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE access_token ALTER expires_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_author ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_author ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_publishing_brand ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_publishing_brand ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_publishing_house ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_publishing_house ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_series ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_series ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_user_data ALTER release_date TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_user_data ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE book_user_data ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE city ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE city ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE product ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE product ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');;
        $this->addSql('ALTER TABLE product_price ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE product_stock ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE product_user_data ALTER not_available_date_from TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE product_user_data ALTER available_date_from TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE product_user_data ALTER release_date TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE product_user_data ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE product_user_data ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE source_product ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE source_product ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE source_product_type ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE source_product_type ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE source_product_user_data ALTER date_updated TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE source_product_user_data ALTER date_created TYPE TIMESTAMP(0) WITH TIME ZONE');

        $this->addSql('ALTER TABLE product_price ALTER date_created_string TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE product_stock ALTER date_created_string TYPE VARCHAR(50)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE access_token ALTER expires_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_author ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_author ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_publishing_brand ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_publishing_brand ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_publishing_house ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_publishing_house ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_series ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_series ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_user_data ALTER release_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_user_data ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE book_user_data ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE city ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE city ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE product ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE product ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE product_price ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE product_stock ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE product_user_data ALTER not_available_date_from TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE product_user_data ALTER available_date_from TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE product_user_data ALTER release_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE product_user_data ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE product_user_data ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE source_product ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE source_product ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE source_product_type ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE source_product_type ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE source_product_user_data ALTER date_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE source_product_user_data ALTER date_created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');

        $this->addSql('ALTER TABLE product_price ALTER date_created_string TYPE VARCHAR(25)');
        $this->addSql('ALTER TABLE product_stock ALTER date_created_string TYPE VARCHAR(25)');
    }
}
