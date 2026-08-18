<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260813134000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавление уникального индекса в таблице product на поля shop_id, shop_product_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_product_shop_id_product_id');
        $this->addSql('CREATE UNIQUE INDEX IDX_UNIQUE_SHOP_SHOP_PRODUCT ON product (shop_id, shop_product_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_UNIQUE_SHOP_SHOP_PRODUCT');
        $this->addSql('CREATE INDEX idx_product_shop_id_product_id ON product (shop_id, shop_product_id)');
    }
}
