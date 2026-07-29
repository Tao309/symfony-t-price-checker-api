<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260729115319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавление составного индекса на поля shop_id, shop_product_id в таблице product';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_d34a04ad4d16c4dd');
        $this->addSql('CREATE INDEX product_shop_id_product_id_idx ON product (shop_id, shop_product_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX product_shop_id_product_id_idx');
        $this->addSql('CREATE INDEX idx_d34a04ad4d16c4dd ON product (shop_id)');
    }
}
