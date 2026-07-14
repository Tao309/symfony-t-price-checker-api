<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260714130631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '<<<EOF
        1) Добавлена таблица book_user_data
        EOF ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE book_user_data (release_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, listen_price_value INT DEFAULT NULL, comment TEXT DEFAULT NULL, date_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, book_id INT NOT NULL, user_created_id INT NOT NULL, PRIMARY KEY (book_id, user_created_id))');
        $this->addSql('COMMENT ON TABLE book_user_data IS \'Пользовательские данные по книгам\'');
        $this->addSql('CREATE INDEX IDX_C5DCA9EC16A2B381 ON book_user_data (book_id)');
        $this->addSql('CREATE INDEX IDX_C5DCA9ECF987D8A8 ON book_user_data (user_created_id)');
        $this->addSql('CREATE UNIQUE INDEX bud_book_user ON book_user_data (book_id, user_created_id)');
        $this->addSql('ALTER TABLE book_user_data ADD CONSTRAINT FK_C5DCA9EC16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_user_data ADD CONSTRAINT FK_C5DCA9ECF987D8A8 FOREIGN KEY (user_created_id) REFERENCES "user" (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book_user_data DROP CONSTRAINT FK_C5DCA9EC16A2B381');
        $this->addSql('ALTER TABLE book_user_data DROP CONSTRAINT FK_C5DCA9ECF987D8A8');
        $this->addSql('DROP TABLE book_user_data');
    }
}
