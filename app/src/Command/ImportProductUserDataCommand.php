<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Exception\SkipRowImportException;
use App\Entity\ProductUserData;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'import:product_user_data',
    description: 'Импортируем пользовательские данные по товаров',
)]
class ImportProductUserDataCommand extends CommonImportCommand
{
    public const string COMMAND_LABEL = 'пользовательские данные по товаров';

    private const string FIELD_PRODUCT_ID = 'product_id';
    private const string FIELD_USER_CREATED_ID = 'user_created_id';
    private const string FIELD_AVAILABLE = 'available';
    private const string FIELD_NOT_AVAILABLE_DATE_FROM = 'not_available_date_from';
    private const string FIELD_AVAILABLE_DATE_FROM = 'available_date_from';
    private const string FIELD_LISTEN_PRICE_VALUE = 'listen_price_value';
    private const string FIELD_LISTEN_QTY_VALUE = 'listen_qty_value';
    private const string FIELD_RELEASE_DATE = 'release_date';
    private const string FIELD_IS_ARCHIVE = 'is_archive';
    private const string FIELD_COMMENT = 'comment';
    private const string FIELD_DATE_UPDATED = 'date_updated';
    private const string FIELD_DATE_CREATED = 'date_created';

    protected string $filePath = '/migrations/import/product_user_data.csv';

    private array $notFound = [];

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly UserRepository $userRepository,
        #[Autowire('%kernel.project_dir%')]
        protected string $projectDir,
        protected EntityManagerInterface $em,
    ) {
        parent::__construct($projectDir, $em);
    }

    protected function runAtEnd(): void
    {
        if (!\count($this->notFound)) {
            return;
        }

        $this->io->error('Не найдено для связи с другой моделью: ' . \count($this->notFound));

        if ($this->showParsingLog) {
            $this->io->table(['Поле', 'product', 'user', 'date'], $this->notFound);
        }
    }

    protected function fillImportRow(array $row): void
    {
        $this->importData[] = [
            self::FIELD_PRODUCT_ID => (int) $row[0],
            self::FIELD_USER_CREATED_ID => (int) $row[1],
            self::FIELD_AVAILABLE => (bool) $row[2],
            self::FIELD_NOT_AVAILABLE_DATE_FROM => $row[3],
            self::FIELD_AVAILABLE_DATE_FROM => $row[4],
            self::FIELD_LISTEN_PRICE_VALUE => $row[5] ?? null,
            self::FIELD_LISTEN_QTY_VALUE => $row[6] ?? null,
            self::FIELD_RELEASE_DATE => $row[7],
            self::FIELD_IS_ARCHIVE => (bool) $row[8],
            self::FIELD_COMMENT => trim($row[9]),
            self::FIELD_DATE_UPDATED => $row[10],
            self::FIELD_DATE_CREATED => $row[11],
        ];
    }

    protected function createEntityByImportRowData(array $row): ProductUserData
    {
        $entity = new ProductUserData()
            ->setAvailable($row[self::FIELD_AVAILABLE])
            ->setNotAvailableDateFrom(
                $row[self::FIELD_NOT_AVAILABLE_DATE_FROM]
                    ? new \DateTimeImmutable($row[self::FIELD_NOT_AVAILABLE_DATE_FROM])
                    : null
            )
            ->setAvailableDateFrom(
                $row[self::FIELD_AVAILABLE_DATE_FROM]
                    ? new \DateTimeImmutable($row[self::FIELD_AVAILABLE_DATE_FROM])
                    : null
            )
            ->setListenPriceValue($row[self::FIELD_LISTEN_PRICE_VALUE])
            ->setListenQtyValue($row[self::FIELD_LISTEN_QTY_VALUE])
            ->setReleaseDate(
                $row[self::FIELD_RELEASE_DATE]
                    ? new \DateTimeImmutable($row[self::FIELD_RELEASE_DATE])
                    : null
            )
            ->setIsArchive($row[self::FIELD_IS_ARCHIVE])
            ->setComment($row[self::FIELD_COMMENT])
            ->setDateUpdated(new \DateTimeImmutable($row[self::FIELD_DATE_UPDATED]))
            ->setDateCreated(new \DateTimeImmutable($row[self::FIELD_DATE_CREATED]))
        ;

        // Проставление товара
        if (empty($row[self::FIELD_PRODUCT_ID])) {
            throw new \RuntimeException(
                \sprintf(
                    'Поле product пустое для ProductUserData с userId = %s',
                    $row[self::FIELD_USER_CREATED_ID]
                )
            );
        }

        $product = $this->productRepository->find($row[self::FIELD_PRODUCT_ID]);

        if (empty($product)) {
            $this->notFound[] = [
                'product',
                $row[self::FIELD_PRODUCT_ID],
                $row[self::FIELD_USER_CREATED_ID],
                $row[self::FIELD_DATE_CREATED],
            ];

            throw new SkipRowImportException();
        }

        $entity->setProduct($product);

        // Проставление пользователя
        if (empty($row[self::FIELD_USER_CREATED_ID])) {
            throw new \RuntimeException(
                \sprintf(
                    'Поле userCreated пустое для ProductUserData с productId = %s и dateCreated = %s',
                    $row[self::FIELD_PRODUCT_ID],
                    $row[self::FIELD_DATE_CREATED]
                )
            );
        }

        $user = $this->userRepository->find($row[self::FIELD_USER_CREATED_ID]);

        if (empty($user)) {
            throw new \RuntimeException(
                \sprintf(
                    'Не найден userCreated %s для ProductUserData с productId = %s и dateCreated = %s',
                    $row[self::FIELD_USER_CREATED_ID],
                    $row[self::FIELD_PRODUCT_ID],
                    $row[self::FIELD_DATE_CREATED]
                )
            );
        }

        $entity->setUserCreated($user);

        return $entity;
    }
}
