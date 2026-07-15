<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260715071624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '<<<EOF
        1) Добавлена таблица source_product_user_data
        EOF ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE source_product_user_data (listen_price_value INT DEFAULT NULL, comment TEXT DEFAULT NULL, date_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, source_product_id INT NOT NULL, user_created_id INT NOT NULL, PRIMARY KEY (source_product_id, user_created_id))');
        $this->addSql('CREATE INDEX IDX_86F07B3C3930177E ON source_product_user_data (source_product_id)');
        $this->addSql('CREATE INDEX IDX_86F07B3CF987D8A8 ON source_product_user_data (user_created_id)');
        $this->addSql('CREATE UNIQUE INDEX spud_source_product_user ON source_product_user_data (source_product_id, user_created_id)');
        $this->addSql('COMMENT ON TABLE source_product_user_data IS \'Пользовательские данные по источникам товара\'');
        $this->addSql('ALTER TABLE source_product_user_data ADD CONSTRAINT FK_86F07B3C3930177E FOREIGN KEY (source_product_id) REFERENCES source_product (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE source_product_user_data ADD CONSTRAINT FK_86F07B3CF987D8A8 FOREIGN KEY (user_created_id) REFERENCES "user" (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE source_product_user_data DROP CONSTRAINT FK_86F07B3C3930177E');
        $this->addSql('ALTER TABLE source_product_user_data DROP CONSTRAINT FK_86F07B3CF987D8A8');
        $this->addSql('DROP TABLE source_product_user_data');
    }
}
