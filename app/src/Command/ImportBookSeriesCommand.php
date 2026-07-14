<?php

namespace App\Command;

use App\Entity\BookSeries;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'import:book_series',
    description: 'Импортируем список серий по книгам',
)]
class ImportBookSeriesCommand extends CommonImportCommand
{
    public const string COMMAND_LABEL = 'серии по книгам';

    private const string FIELD_ID = 'id';
    private const string FIELD_NAME = 'name';
    private const string FIELD_DATE_UPDATED = 'date_updated';
    private const string FIELD_DATE_CREATED = 'date_created';

    protected string $filePath = '/migrations/import/book_series.csv';

    protected function runBeforeFlush(): void
    {
        $metadata = $this->em->getClassMetaData(BookSeries::class);
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    }

    protected function fillImportRow(array $row): void
    {
        $this->importData[] = [
            self::FIELD_ID => (int) $row[0],
            self::FIELD_NAME => $row[1],
            self::FIELD_DATE_UPDATED => $row[2],
            self::FIELD_DATE_CREATED => $row[3],
        ];
    }

    protected function createEntityByImportRowData(array $row): mixed
    {
        $entity = new BookSeries();
        $entity->setId($row[self::FIELD_ID]);
        $entity->setName($row[self::FIELD_NAME]);
        $entity->setDateUpdated(new \DateTimeImmutable($row[self::FIELD_DATE_UPDATED]));
        $entity->setDateCreated(new \DateTimeImmutable($row[self::FIELD_DATE_CREATED]));

        return $entity;
    }
}
