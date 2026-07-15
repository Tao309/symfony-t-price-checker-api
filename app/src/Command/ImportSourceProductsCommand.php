<?php

namespace App\Command;

use App\Entity\SourceProduct;
use App\Repository\SourceProductTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'import:source_products',
    description: 'Импортируем источники товаров',
)]
class ImportSourceProductsCommand extends CommonImportCommand
{
    public const string COMMAND_LABEL = 'источники товаров';

    private const string FIELD_ID = 'id';
    private const string FIELD_SOURCE_PRODUCT_TYPE_ID = 'source_product_type_id';
    private const string FIELD_TITLE = 'title';
    private const string FIELD_USER_CREATED_ID = 'user_created_id';
    private const string FIELD_DATE_UPDATED = 'date_updated';
    private const string FIELD_DATE_CREATED = 'date_created';

    protected string $filePath = '/migrations/import/source_products.csv';

    public function __construct(
        private readonly SourceProductTypeRepository $sourceProductTypeRepository,
        private readonly UserRepository $userRepository,
        #[Autowire('%kernel.project_dir%')] protected string $projectDir,
        protected EntityManagerInterface $em,
    ) {
        parent::__construct($projectDir, $em);
    }

    protected function runBeforeFlush(): void
    {
        $metadata = $this->em->getClassMetaData(SourceProduct::class);
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    }

    protected function fillImportRow(array $row): void
    {
        $this->importData[] = [
            self::FIELD_ID => (int) $row[0],
            self::FIELD_SOURCE_PRODUCT_TYPE_ID => (int) $row[1],
            self::FIELD_TITLE => $row[2],
            self::FIELD_USER_CREATED_ID => (int) $row[3],
            self::FIELD_DATE_UPDATED => $row[4],
            self::FIELD_DATE_CREATED => $row[5],
        ];
    }

    protected function createEntityByImportRowData(array $row): SourceProduct
    {
        $entity = new SourceProduct()
            ->setId($row[self::FIELD_ID])
            ->setTitle($row[self::FIELD_TITLE])
            ->setDateUpdated(new \DateTimeImmutable($row[self::FIELD_DATE_UPDATED]))
            ->setDateCreated(new \DateTimeImmutable($row[self::FIELD_DATE_CREATED]))
        ;

        // Проставление типа источника товара
        if (empty($row[self::FIELD_SOURCE_PRODUCT_TYPE_ID])) {
            throw new \RuntimeException(sprintf('Поле sourceProductType пустое для SourceProduct с id = %s', $row[self::FIELD_ID]));
        }

        $sourceProductType = $this->sourceProductTypeRepository->find($row[self::FIELD_SOURCE_PRODUCT_TYPE_ID]);

        if (empty($sourceProductType)) {
            throw new \RuntimeException(sprintf('Не найден sourceProductType с id = %s для SourceProduct с id = %s', $row[self::FIELD_SOURCE_PRODUCT_TYPE_ID], $row[self::FIELD_ID]));
        }

        $entity->setSourceProductType($sourceProductType);

        // Проставление пользователя
        if (empty($row[self::FIELD_USER_CREATED_ID])) {
            throw new \RuntimeException(sprintf('Поле userCreated пустое для SourceProduct с id = %s', $row[self::FIELD_ID]));
        }

        $user = $this->userRepository->find($row[self::FIELD_USER_CREATED_ID]);

        if (empty($user)) {
            throw new \RuntimeException(sprintf('Не найден userCreated с id = %s для SourceProduct с id = %s', $row[self::FIELD_USER_CREATED_ID], $row[self::FIELD_ID]));
        }

        $entity->setUserCreated($user);

        return $entity;
    }
}
