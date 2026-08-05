<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260805071552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Поле release_date в таблице book_user_data может быть null';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book_user_data ALTER release_date DROP NOT NULL');
        $this->addSql('ALTER INDEX product_shop_id_product_id_idx RENAME TO idx_product_shop_id_product_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book_user_data ALTER release_date SET NOT NULL');
        $this->addSql('ALTER INDEX idx_product_shop_id_product_id RENAME TO product_shop_id_product_id_idx');
    }
}
