<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Exception\SkipRowImportException;
use App\Entity\BookUserData;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'import:book_user_data',
    description: 'Импортируем пользовательские данные по книгам',
)]
class ImportBookUserDataCommand extends CommonImportCommand
{
    public const string COMMAND_LABEL = 'пользовательские данные по книгам';

    private const string FIELD_BOOK_ID = 'book_id';
    private const string FIELD_USER_CREATED_ID = 'user_created_id';
    private const string FIELD_RELEASE_DATE = 'release_date';
    private const string FIELD_LISTEN_PRICE_VALUE = 'listen_price_value';
    private const string FIELD_COMMENT = 'comment';
    private const string FIELD_DATE_UPDATED = 'date_updated';
    private const string FIELD_DATE_CREATED = 'date_created';

    protected string $filePath = '/migrations/import/book_user_data.csv';

    private array $notFound = [];

    public function __construct(
        private readonly BookRepository $bookRepository,
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
            $this->io->table(['Поле', 'book', 'user', 'date'], $this->notFound);
        }
    }

    protected function fillImportRow(array $row): void
    {
        $this->importData[] = [
            self::FIELD_BOOK_ID => (int) $row[0],
            self::FIELD_USER_CREATED_ID => (int) $row[1],
            self::FIELD_RELEASE_DATE => $row[2],
            self::FIELD_LISTEN_PRICE_VALUE => $row[3],
            self::FIELD_COMMENT => trim($row[4]),
            self::FIELD_DATE_UPDATED => $row[5],
            self::FIELD_DATE_CREATED => $row[6],
        ];
    }

    protected function createEntityByImportRowData(array $row): BookUserData
    {
        $entity = new BookUserData()
            ->setReleaseDate(new \DateTimeImmutable($row[self::FIELD_RELEASE_DATE]))
            ->setListenPriceValue($row[self::FIELD_LISTEN_PRICE_VALUE])
            ->setComment($row[self::FIELD_COMMENT])
            ->setDateUpdated(new \DateTimeImmutable($row[self::FIELD_DATE_UPDATED]))
            ->setDateCreated(new \DateTimeImmutable($row[self::FIELD_DATE_CREATED]))
        ;

        // Проставление книги
        if (empty($row[self::FIELD_BOOK_ID])) {
            throw new \RuntimeException(
                \sprintf(
                    'Поле product пустое для BookUserData с userId = %s',
                    $row[self::FIELD_USER_CREATED_ID]
                )
            );
        }

        $book = $this->bookRepository->find($row[self::FIELD_BOOK_ID]);

        if (empty($book)) {
            $this->notFound[] = [
                'book',
                $row[self::FIELD_BOOK_ID],
                $row[self::FIELD_USER_CREATED_ID],
                $row[self::FIELD_DATE_CREATED],
            ];

            throw new SkipRowImportException();
        }

        $entity->setBook($book);

        // Проставление пользователя
        if (empty($row[self::FIELD_USER_CREATED_ID])) {
            throw new \RuntimeException(
                \sprintf(
                    'Поле userCreated пустое для BookUserData с bookId = %s и dateCreated = %s',
                    $row[self::FIELD_BOOK_ID],
                    $row[self::FIELD_DATE_CREATED]
                )
            );
        }

        $user = $this->userRepository->find($row[self::FIELD_USER_CREATED_ID]);

        if (empty($user)) {
            throw new \RuntimeException(
                \sprintf(
                    'Не найден userCreated %s для BookUserData с bookId = %s и dateCreated = %s',
                    $row[self::FIELD_USER_CREATED_ID],
                    $row[self::FIELD_BOOK_ID],
                    $row[self::FIELD_DATE_CREATED]
                )
            );
        }

        $entity->setUserCreated($user);

        return $entity;
    }
}
