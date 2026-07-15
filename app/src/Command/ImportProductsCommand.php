<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Product;
use App\Repository\BookRepository;
use App\Repository\CityRepository;
use App\Repository\ShopRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'import:products',
    description: 'Импортируем список товаров',
)]
class ImportProductsCommand extends CommonImportCommand
{
    public const string COMMAND_LABEL = 'товары';

    private const string FIELD_ID = 'id';
    private const string FIELD_SHOP_PRODUCT_ID = 'shop_product_id';
    private const string FIELD_SHOP_PRODUCT_CODE = 'shop_product_code';
    private const string FIELD_SOURCE_PRODUCT_ID = 'source_product_id';
    private const string FIELD_BOOK_ID = 'book_id';
    private const string FIELD_SHOP_ID = 'shop_id';
    private const string FIELD_CITY_ID = 'city_id';
    private const string FIELD_TITLE = 'title';
    private const string FIELD_AUTHOR_USER_ID = 'author_user_id';
    private const string FIELD_DATE_UPDATED = 'date_updated';
    private const string FIELD_DATE_CREATED = 'date_created';

    protected string $filePath = '/migrations/import/products.csv';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CityRepository $cityRepository,
        private readonly ShopRepository $shopRepository,
        private readonly BookRepository $bookRepository,
        #[Autowire('%kernel.project_dir%')]
        protected string $projectDir,
        protected EntityManagerInterface $em,
    ) {
        parent::__construct($projectDir, $em);
    }

    protected function runBeforeImport(): void
    {
        // Проверяем целостность всех данных в строках импорта
        $parsingErrors = [];

        foreach ($this->importData as $data) {
            try {
                new \DateTimeImmutable($data[self::FIELD_DATE_UPDATED]);
            } catch (\Throwable $e) {
                $parsingErrors[] = [self::FIELD_DATE_UPDATED, $data[self::FIELD_ID]];
            }

            try {
                new \DateTimeImmutable($data[self::FIELD_DATE_CREATED]);
            } catch (\Throwable $e) {
                $parsingErrors[] = [self::FIELD_DATE_CREATED, $data[self::FIELD_ID], $data[self::FIELD_TITLE]];
            }
        }

        if ($parsingErrors) {
            $this->io->error('Ошибки при парсинге файла');
            $this->io->table(['field', 'product_id', 'title'], $parsingErrors);

            throw new \RuntimeException('Ошибки при парсинге файла');
        }
    }

    protected function runBeforeFlush(): void
    {
        $metadata = $this->em->getClassMetaData(Product::class);
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    }

    protected function fillImportRow(array $row): void
    {
        $this->importData[] = [
            self::FIELD_ID => (int) $row[0],
            self::FIELD_SHOP_PRODUCT_ID => $row[1],
            self::FIELD_SHOP_PRODUCT_CODE => $row[2],
            self::FIELD_SOURCE_PRODUCT_ID => $row[3],
            self::FIELD_BOOK_ID => $row[4],
            self::FIELD_SHOP_ID => $row[5],
            self::FIELD_CITY_ID => $row[6],
            self::FIELD_TITLE => $row[7],
            self::FIELD_AUTHOR_USER_ID => $row[8],
            self::FIELD_DATE_UPDATED => $row[9],
            self::FIELD_DATE_CREATED => $row[10],
        ];
    }

    protected function createEntityByImportRowData(array $row): Product
    {
        $entity = new Product()
            ->setId($row[self::FIELD_ID])
            ->setShopProductId($row[self::FIELD_SHOP_PRODUCT_ID])
            ->setShopProductCode($row[self::FIELD_SHOP_PRODUCT_CODE])
            ->setTitle($row[self::FIELD_TITLE])
            ->setDateUpdated(new \DateTimeImmutable($row[self::FIELD_DATE_UPDATED]))
            ->setDateCreated(new \DateTimeImmutable($row[self::FIELD_DATE_CREATED]))
        ;

        if (!empty($row[self::FIELD_BOOK_ID])) {
            $book = $this->bookRepository->find($row[self::FIELD_BOOK_ID]);

            if (empty($book)) {
                throw new \RuntimeException(
                    \sprintf(
                        'Не найдена book %s для товара %s',
                        $row[self::FIELD_BOOK_ID],
                        $row[self::FIELD_ID]
                    )
                );
            }

            $entity->setBook($book);
        }

        if (empty($row[self::FIELD_SHOP_ID])) {
            throw new \RuntimeException(\sprintf('Поле shop пустое для товара %s', $row[self::FIELD_ID]));
        }

        $shop = $this->shopRepository->find($row[self::FIELD_SHOP_ID]);

        if (empty($shop)) {
            throw new \RuntimeException(
                \sprintf(
                    'Не найден shop %s для товара %s',
                    $row[self::FIELD_SHOP_ID],
                    $row[self::FIELD_ID]
                )
            );
        }

        $entity->setShop($shop);

        if (!empty($row[self::FIELD_CITY_ID])) {
            $city = $this->cityRepository->find($row[self::FIELD_CITY_ID]);

            if (empty($city)) {
                throw new \RuntimeException(
                    \sprintf(
                        'Не найден city %s для товара %s',
                        $row[self::FIELD_CITY_ID],
                        $row[self::FIELD_ID]
                    )
                );
            }

            $entity->setCity($city);
        }

        if (empty($row[self::FIELD_AUTHOR_USER_ID])) {
            throw new \RuntimeException(\sprintf('Поле userCreated пустое для товара %s', $row[self::FIELD_ID]));
        }

        $user = $this->userRepository->find($row[self::FIELD_AUTHOR_USER_ID]);

        if (empty($user)) {
            throw new \RuntimeException(
                \sprintf(
                    'Не найден userCreated %s для товара %s',
                    $row[self::FIELD_AUTHOR_USER_ID],
                    $row[self::FIELD_ID]
                )
            );
        }

        $entity->setUserCreated($user);

        return $entity;
    }
}
