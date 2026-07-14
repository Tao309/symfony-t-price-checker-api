<?php

namespace App\Command;

use App\Entity\SourceProductType;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'import:source_product_types',
    description: 'Импортируем список типов источников товаров',
)]
class ImportSourceProductTypesCommand extends CommonImportCommand
{
    public const string COMMAND_LABEL = 'типы источников товаров';

    private const string FIELD_ID = 'id';
    private const string FIELD_CODE = 'code';
    private const string FIELD_NAME = 'name';
    private const string FIELD_DATE_CREATED = 'date_created';

    protected string $filePath = '/migrations/import/source_product_types.csv';

    protected function runBeforeFlush(): void
    {
        $metadata = $this->em->getClassMetaData(SourceProductType::class);
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    }

    protected function fillImportRow(array $row): void
    {
        $this->importData[] = [
            self::FIELD_ID => (int) $row[0],
            self::FIELD_CODE => $row[1],
            self::FIELD_NAME => $row[2],
            self::FIELD_DATE_CREATED => $row[3],
        ];
    }

    protected function createEntityByImportRowData(array $row): mixed
    {
        $entity = new SourceProductType();
        $entity->setId($row[self::FIELD_ID]);
        $entity->setCode($row[self::FIELD_CODE]);
        $entity->setName($row[self::FIELD_NAME]);
        $entity->setDateCreated(new \DateTimeImmutable($row[self::FIELD_DATE_CREATED]));

        return $entity;
    }
}
