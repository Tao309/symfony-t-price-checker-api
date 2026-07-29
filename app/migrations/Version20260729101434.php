<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260729101434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавление уникального индекса по полям product_id, user_created_id в таблицу product_user_data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX pud_product_user ON product_user_data (product_id, user_created_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX pud_product_user');
    }
}
