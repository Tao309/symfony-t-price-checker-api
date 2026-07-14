<?php

namespace App\Command;

use App\Command\Exception\SkipRowImportException;
use App\Entity\ProductStock;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'import:product_stocks',
    description: 'Импортируем стоки на товары',
)]
class ImportProductStocksCommand extends CommonImportCommand
{
    public const string COMMAND_LABEL = 'стоки по товарам';

    private const string FIELD_PRODUCT_ID = 'product_id';
    private const string FIELD_QTY = 'qty';
    private const string FIELD_USER_CREATED_ID = 'user_created_id';
    private const string FIELD_DATE_CREATED = 'date_created';
    private const string FIELD_LOG = 'log';

    protected string $filePath = '/migrations/import/product_stocks.csv';

    private array $notFound = [];

    public function __construct(
        private readonly ProductRepository $productRepository,
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
            $this->io->table(['Поле', 'product', 'user', 'date'], $this->notFound);
        }
    }

    protected function fillImportRow(array $row): void
    {
        $this->importData[] = [
            self::FIELD_PRODUCT_ID => (int) $row[0],
            self::FIELD_QTY => $row[1],
            self::FIELD_DATE_CREATED => $row[2],
            self::FIELD_USER_CREATED_ID => (int) $row[3],
            self::FIELD_LOG => $row[4],
        ];
    }

    protected function createEntityByImportRowData(array $row): ProductStock
    {
        $entity = new ProductStock()
            ->setQty($row[self::FIELD_QTY])
            ->setDateCreated(new \DateTimeImmutable($row[self::FIELD_DATE_CREATED]))
            ->setDateCreatedString($row[self::FIELD_DATE_CREATED])
            ->setLog(json_decode($row[self::FIELD_LOG], true))
        ;

        // Проставление товара
        if (empty($row[self::FIELD_PRODUCT_ID])) {
            throw new \RuntimeException(sprintf('Поле product пустое для productStock с userId = %s и dateCreated = %s', $row[self::FIELD_USER_CREATED_ID], $row[self::FIELD_DATE_CREATED]));
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
            throw new \RuntimeException(sprintf('Поле userCreated пустое для productStock с productId = %s и dateCreated = %s', $row[self::FIELD_PRODUCT_ID], $row[self::FIELD_DATE_CREATED]));
        }

        $user = $this->userRepository->find($row[self::FIELD_USER_CREATED_ID]);

        if (empty($user)) {
            throw new \RuntimeException(sprintf('Не найден userCreated %s для productStock с productId = %s и dateCreated = %s', $row[self::FIELD_USER_CREATED_ID], $row[self::FIELD_PRODUCT_ID], $row[self::FIELD_DATE_CREATED]));
        }

        $entity->setUserCreated($user);

        return $entity;
    }
}
