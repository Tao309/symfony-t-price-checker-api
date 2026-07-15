<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Book;
use App\Entity\BookAuthor;
use App\Repository\BookAuthorRepository;
use App\Repository\BookBindingTypeRepository;
use App\Repository\BookPublishingBrandRepository;
use App\Repository\BookPublishingHouseRepository;
use App\Repository\BookSeriesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'import:books',
    description: 'Импортируем список книг и авторов',
)]
class ImportBooksCommand extends CommonImportCommand
{
    public const string COMMAND_LABEL = 'книги';

    private const string FIELD_ID = 'id';
    private const string FIELD_TITLE = 'title';
    private const string FIELD_AUTHOR = 'author';
    private const string FIELD_ORIGINAL_TITLE = 'original_title';
    private const string FIELD_ORIGINAL_AUTHOR = 'original_author';
    private const string FIELD_ISBN = 'isbn';
    private const string FIELD_PAGES = 'pages';
    private const string FIELD_CIRCULATION = 'circulation';
    private const string FIELD_SIZE = 'size';
    private const string FIELD_BINDING_TYPE_ID = 'binding_type_id';
    private const string FIELD_PUBLISHING_HOUSE_ID = 'publishing_house_id';
    private const string FIELD_PUBLISHING_BRAND_ID = 'publishing_brand_id';
    private const string FIELD_BOOK_SERIES_ID = 'book_series_id';
    private const string FIELD_PUBLISH_YEAR = 'publish_year';
    private const string FIELD_LIVELIB_ID = 'livelib_id';
    private const string FIELD_GOODREADS_ID = 'goodreads_id';
    private const string FIELD_FANTLAB_ID = 'fantlab_id';
    private const string FIELD_LIVELIB_RATING = 'livelib_rating';
    private const string FIELD_GOODREADS_RATING = 'goodreads_rating';
    private const string FIELD_AUTHOR_USER_ID = 'author_user_id';
    private const string FIELD_DATE_UPDATED = 'date_updated';
    private const string FIELD_DATE_CREATED = 'date_created';

    protected string $filePath = '/migrations/import/books.csv';

    private array $authorAdded = [];

    public function __construct(
        private readonly BookAuthorRepository $bookAuthorRepository,
        private readonly BookBindingTypeRepository $bookBindingTypeRepository,
        private readonly UserRepository $userRepository,
        private readonly BookSeriesRepository $bookSeriesRepository,
        private readonly BookPublishingBrandRepository $bookPublishingBrandRepository,
        private readonly BookPublishingHouseRepository $bookPublishingHouseRepository,
        #[Autowire('%kernel.project_dir%')]
        protected string $projectDir,
        protected EntityManagerInterface $em,
    ) {
        parent::__construct($projectDir, $em);
    }

    protected function runBeforeFlush(): void
    {
        $metadata = $this->em->getClassMetaData(Book::class);
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    }

    protected function fillImportRow(array $row): void
    {
        $this->importData[] = [
            self::FIELD_ID => (int) $row[0],
            self::FIELD_TITLE => $row[1],
            self::FIELD_AUTHOR => trim($row[2]),
            self::FIELD_ORIGINAL_TITLE => $row[3],
            self::FIELD_ORIGINAL_AUTHOR => $row[4],
            self::FIELD_ISBN => $row[5],
            self::FIELD_PAGES => $row[6],
            self::FIELD_CIRCULATION => $row[7],
            self::FIELD_SIZE => $row[8],
            self::FIELD_BINDING_TYPE_ID => $row[9],
            self::FIELD_PUBLISHING_HOUSE_ID => $row[10],
            self::FIELD_PUBLISHING_BRAND_ID => $row[11],
            self::FIELD_BOOK_SERIES_ID => $row[12],
            self::FIELD_PUBLISH_YEAR => $row[13],
            self::FIELD_LIVELIB_ID => $row[14],
            self::FIELD_GOODREADS_ID => $row[15],
            self::FIELD_FANTLAB_ID => $row[16],
            self::FIELD_LIVELIB_RATING => $row[17],
            self::FIELD_GOODREADS_RATING => $row[18],
            self::FIELD_AUTHOR_USER_ID => $row[19],
            self::FIELD_DATE_UPDATED => $row[20],
            self::FIELD_DATE_CREATED => $row[21],
        ];
    }

    protected function runBeforeImport(): void
    {
        // Добавляем авторов книг из файла с книгами
        $batchSize = 500;
        $totalRecords = \count($this->importData);

        $this->io->progressStart($totalRecords);

        if (!$this->isFake) {
            $this->em->beginTransaction();
        }

        $this->io->title('Начало добавления авторов книг');

        try {
            if (!$this->isFake) {
                $this->em->getConnection()->getConfiguration()
                    ->setMiddlewares([new \Doctrine\DBAL\Logging\Middleware(new \Psr\Log\NullLogger())]);
            }

            for ($i = 0; $i < $totalRecords; ++$i) {
                $authorValue = $this->importData[$i][self::FIELD_AUTHOR];

                if (empty($authorValue)) {
                    throw new \RuntimeException(
                        \sprintf('Поле автора пустое в книге %s', $this->importData[$i][self::FIELD_ID])
                    );
                }

                $this->io->progressAdvance();

                [$firstName, $lastName] = $this->splitAuthorFullName($authorValue);

                $author = new BookAuthor();
                $author->setFirstName($firstName);
                $author->setLastName($lastName);

                if (isset($this->authorAdded[$author->getFullName()])) {
                    continue;
                }

                $this->authorAdded[$author->getFullName()] = [$author->getFirstName(), $author->getLastName()];

                if (!$this->isFake) {
                    $this->em->persist($author);
                }

                if (($i % $batchSize) === 0) {
                    if (!$this->isFake) {
                        $this->em->flush();
                        $this->em->clear();
                    }
                }
            }

            if (!$this->isFake) {
                $this->em->flush();
                $this->em->clear();
                $this->em->commit();
            }

            $this->io->success('Завершено добавление авторов книг');
            $this->io->section('Добавлено BookAuthor: ' . \count($this->authorAdded));
            if ($this->showParsingLog) {
                $this->io->table(['firstName', 'lastName'], $this->authorAdded);
            }
        } catch (\Throwable $e) {
            if (!$this->isFake) {
                $this->em->rollback();
            }

            throw new \RuntimeException('Ошибка при записи в БД BookAuthor: ' . $e->getMessage());
        }

        $this->io->progressFinish();
    }

    protected function createEntityByImportRowData(array $row): mixed
    {
        $entity = (new Book())
            ->setId($row[self::FIELD_ID])
            ->setTitle($row[self::FIELD_TITLE])
            ->setOriginalTitle($row[self::FIELD_ORIGINAL_TITLE])
            ->setIsbn($row[self::FIELD_ISBN])
            ->setPages($row[self::FIELD_PAGES])
            ->setCirculation($row[self::FIELD_CIRCULATION])
            ->setSize($row[self::FIELD_SIZE])
            ->setPublishYear($row[self::FIELD_PUBLISH_YEAR])
            ->setLivelibId($row[self::FIELD_LIVELIB_ID])
            ->setGoodreadsId($row[self::FIELD_GOODREADS_ID])
            ->setFantlabId($row[self::FIELD_FANTLAB_ID])
            ->setLivelibRating($row[self::FIELD_LIVELIB_RATING])
            ->setGoodreadsRating($row[self::FIELD_GOODREADS_RATING])
            ->setDateUpdated(new \DateTimeImmutable($row[self::FIELD_DATE_UPDATED]))
            ->setDateCreated(new \DateTimeImmutable($row[self::FIELD_DATE_CREATED]))
        ;

        $this->setAuthor($entity, $row);
        $this->setBindingType($entity, $row[self::FIELD_BINDING_TYPE_ID]);
        $this->setPublishingHouse($entity, $row[self::FIELD_PUBLISHING_HOUSE_ID]);
        $this->setPublishingBrand($entity, $row[self::FIELD_PUBLISHING_BRAND_ID]);
        $this->setBookSeries($entity, $row[self::FIELD_BOOK_SERIES_ID]);
        $this->setUser($entity, $row[self::FIELD_AUTHOR_USER_ID]);

        return $entity;
    }

    private function splitAuthorFullName(string $authorFullName): array
    {
        $authorFullName = trim($authorFullName);

        if (str_contains($authorFullName, ',')) {
            return [$authorFullName, null];
        }

        $authorSplit = explode(' ', $authorFullName);

        if (\count($authorSplit) > 1) {
            $firstNameSplit = \array_slice($authorSplit, 0, -1);

            return [
                trim(implode(' ', $firstNameSplit)),
                trim(end($authorSplit)),
            ];
        }

        return [$authorFullName, null];
    }

    private function setAuthor(Book $book, array $row): void
    {
        $authorValue = $row[self::FIELD_AUTHOR];

        [$firstName, $lastName] = $this->splitAuthorFullName($authorValue);

        $foundAuthor = $this->bookAuthorRepository->findOneBy([
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);

        if (empty($foundAuthor)) {
            throw new \RuntimeException(
                \sprintf(
                    'Не найден автор "%s" для книги %s',
                    $row[self::FIELD_AUTHOR],
                    $row[self::FIELD_ID]
                )
            );
        }

        $book->setBookAuthor($foundAuthor);
    }

    private function setBindingType(Book $book, ?string $bindingTypeId): void
    {
        if (empty($bindingTypeId)) {
            throw new \RuntimeException('BindingType cannot be empty');
        }

        $foundBindingType = $this->bookBindingTypeRepository->find($bindingTypeId);

        if (empty($foundBindingType)) {
            throw new \RuntimeException(\sprintf('BindingType %s is not found in repository', $bindingTypeId));
        }

        $book->setBindingType($foundBindingType);
    }

    private function setPublishingHouse(Book $book, ?string $publishingHouseId = null): void
    {
        if (empty($publishingHouseId)) {
            return;
        }

        $publishingHouse = $this->bookPublishingHouseRepository->find($publishingHouseId);

        if (empty($publishingHouse)) {
            throw new \RuntimeException(\sprintf('PublishingHouse %s is not found in repository', $publishingHouseId));
        }

        $book->setPublishingHouse($publishingHouse);
    }

    private function setPublishingBrand(Book $book, ?string $publishingBrandId = null): void
    {
        if (empty($publishingBrandId)) {
            return;
        }

        $publishingBrand = $this->bookPublishingBrandRepository->find($publishingBrandId);

        if (empty($publishingBrand)) {
            throw new \RuntimeException(\sprintf('PublishingBrand %s is not found in repository', $publishingBrandId));
        }

        $book->setPublishingBrand($publishingBrand);
    }

    private function setBookSeries(Book $book, ?string $bookSeriesId = null): void
    {
        if (empty($bookSeriesId)) {
            return;
        }

        $bookSeries = $this->bookSeriesRepository->find($bookSeriesId);

        if (empty($bookSeries)) {
            throw new \RuntimeException(\sprintf('BookSeries %s is not found in repository', $bookSeriesId));
        }

        $book->setBookSeries($bookSeries);
    }

    private function setUser(Book $book, string $userId): void
    {
        if (empty($userId)) {
            throw new \RuntimeException('User cannot be empty');
        }

        $user = $this->userRepository->find($userId);

        if (empty($user)) {
            throw new \RuntimeException(\sprintf('User %s is not found in repository', $userId));
        }

        $book->setUserCreated($user);
    }
}
