<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260715075624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавление таблицы product_user_data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE product_user_data (product_id INT NOT NULL, user_created_id INT NOT NULL, available BOOLEAN NOT NULL, not_available_date_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, available_date_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, listen_price_value INT DEFAULT NULL, listen_qty_value SMALLINT DEFAULT NULL, release_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_archive BOOLEAN NOT NULL, comment TEXT DEFAULT NULL, date_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (product_id, user_created_id))');
        $this->addSql('CREATE INDEX IDX_B9D209274584665A ON product_user_data (product_id)');
        $this->addSql('CREATE INDEX IDX_B9D20927F987D8A8 ON product_user_data (user_created_id)');
        $this->addSql('ALTER TABLE product_user_data ADD CONSTRAINT FK_B9D209274584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE product_user_data ADD CONSTRAINT FK_B9D20927F987D8A8 FOREIGN KEY (user_created_id) REFERENCES "user" (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product_user_data DROP CONSTRAINT FK_B9D209274584665A');
        $this->addSql('ALTER TABLE product_user_data DROP CONSTRAINT FK_B9D20927F987D8A8');
        $this->addSql('DROP TABLE product_user_data');
    }
}
