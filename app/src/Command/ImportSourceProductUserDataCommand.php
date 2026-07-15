<?php

namespace App\Command;

use App\Command\Exception\SkipRowImportException;
use App\Entity\SourceProductUserData;
use App\Repository\SourceProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'import:source_product_user_data',
    description: 'Импортируем пользовательские данные по источникам товаров',
)]
class ImportSourceProductUserDataCommand extends CommonImportCommand
{
    public const string COMMAND_LABEL = 'пользовательские данные по источникам товаров';

    private const string FIELD_SOURCE_PRODUCT_ID = 'source_product_id';
    private const string FIELD_USER_CREATED_ID = 'user_created_id';
    private const string FIELD_LISTEN_PRICE_VALUE = 'listen_price_value';
    private const string FIELD_COMMENT = 'comment';
    private const string FIELD_DATE_UPDATED = 'date_updated';
    private const string FIELD_DATE_CREATED = 'date_created';

    protected string $filePath = '/migrations/import/source_product_user_data.csv';

    private array $notFound = [];

    public function __construct(
        private readonly SourceProductRepository $sourceProductRepository,
        private readonly UserRepository $userRepository,
        #[Autowire('%kernel.project_dir%')] protected string $projectDir,
        protected EntityManagerInterface $em,
    ) {
        parent::__construct($projectDir, $em);
    }

    protected function runAtEnd(): void
    {
        if (!count($this->notFound)) {
            return;
        }

        $this->io->error('Не найдено для связи с другой моделью: '.count($this->notFound));

        if ($this->showParsingLog) {
            $this->io->table(['Поле', 'sourceProduct', 'user', 'date'], $this->notFound);
        }
    }

    protected function fillImportRow(array $row): void
    {
        $this->importData[] = [
            self::FIELD_SOURCE_PRODUCT_ID => (int) $row[0],
            self::FIELD_USER_CREATED_ID => (int) $row[1],
            self::FIELD_LISTEN_PRICE_VALUE => $row[2],
            self::FIELD_COMMENT => $row[3],
            self::FIELD_DATE_UPDATED => $row[4],
            self::FIELD_DATE_CREATED => $row[5],
        ];
    }

    protected function createEntityByImportRowData(array $row): SourceProductUserData
    {
        $entity = new SourceProductUserData()
            ->setListenPriceValue($row[self::FIELD_LISTEN_PRICE_VALUE])
            ->setComment($row[self::FIELD_COMMENT])
            ->setDateUpdated(new \DateTimeImmutable($row[self::FIELD_DATE_UPDATED]))
            ->setDateCreated(new \DateTimeImmutable($row[self::FIELD_DATE_CREATED]))
        ;

        // Проставление источника товара
        if (empty($row[self::FIELD_SOURCE_PRODUCT_ID])) {
            throw new \RuntimeException(sprintf('Поле product пустое для SourceProductUserData с userId = %s', $row[self::FIELD_USER_CREATED_ID]));
        }

        $sourceProduct = $this->sourceProductRepository->find($row[self::FIELD_SOURCE_PRODUCT_ID]);

        if (empty($sourceProduct)) {
            $this->notFound[] = [
                'sourceProduct',
                $row[self::FIELD_SOURCE_PRODUCT_ID],
                $row[self::FIELD_USER_CREATED_ID],
                $row[self::FIELD_DATE_CREATED],
            ];

            throw new SkipRowImportException();
        }

        $entity->setSourceProduct($sourceProduct);

        // Проставление пользователя
        if (empty($row[self::FIELD_USER_CREATED_ID])) {
            throw new \RuntimeException(sprintf('Поле userCreated пустое для SourceProductUserData с sourceProductId = %s и dateCreated = %s', $row[self::FIELD_SOURCE_PRODUCT_ID], $row[self::FIELD_DATE_CREATED]));
        }

        $user = $this->userRepository->find($row[self::FIELD_USER_CREATED_ID]);

        if (empty($user)) {
            throw new \RuntimeException(sprintf('Не найден userCreated %s для SourceProductUserData с sourceProductId = %s и dateCreated = %s', $row[self::FIELD_USER_CREATED_ID], $row[self::FIELD_SOURCE_PRODUCT_ID], $row[self::FIELD_DATE_CREATED]));
        }

        $entity->setUserCreated($user);

        return $entity;
    }
}
