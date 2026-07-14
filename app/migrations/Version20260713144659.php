<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260713144659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '<<<EOF
        1) Изменение типов значений в таблице book по полям livelib_id, goodreads_id
        2) Поле size в таблице book может быть null
        EOF ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book ALTER livelib_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE book ALTER goodreads_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE book ALTER size DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book ALTER livelib_id TYPE INT');
        $this->addSql('ALTER TABLE book ALTER goodreads_id TYPE INT');
        $this->addSql('ALTER TABLE book ALTER size SET NOT NULL');
    }
}
