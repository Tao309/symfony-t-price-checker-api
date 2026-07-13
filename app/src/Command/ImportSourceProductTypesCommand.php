<?php

namespace App\Command;

use App\Entity\SourceProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'import:source_product_types',
    description: 'Импортируем список типов источников товаров из файла /migrations/import/source_product_types.csv',
)]
class ImportSourceProductTypesCommand extends Command
{
    private const string FIELD_ID = 'id';
    private const string FIELD_CODE = 'code';
    private const string FIELD_NAME = 'name';
    private const string FIELD_DATE_CREATED = 'date_created';

    public function __construct(
        #[Autowire('%kernel.project_dir%')] private readonly string $projectDir,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('show-parsing-log', null, InputArgument::OPTIONAL, 'Показывать детали парсинга?', 1)
            ->addOption('fake', null, InputArgument::OPTIONAL, 'Фейковый запрос без записи?', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $added = 0;
        $showParsingLog = (bool) $input->getOption('show-parsing-log') ?? 1;
        $isFake = (bool) $input->getOption('fake') ?? 1;

        $io = new SymfonyStyle($input, $output);

        $io->title('Начало импорта типов источников товаров');
        if ($isFake) {
            $io->warning('Фейковый запуск команды');
        }

        $io->section('Поиск данных для импорта из файла');
        $filePath = $this->projectDir.'/migrations/import/source_product_types.csv';
        if (!file_exists($filePath) || !is_readable($filePath)) {
            $io->error(sprintf('Файл "%s" не найден или недоступен к чтению.', $filePath));

            return Command::FAILURE;
        }
        if (($handle = fopen($filePath, 'r')) === false) {
            $io->error('Ошибка при открытии файла.');

            return Command::FAILURE;
        }

        $io->title('Парсинг CSV файла...');

        $headers = fgetcsv($handle, 0, ',');
        if ($showParsingLog) {
            $io->writeln(
                sprintf('Заголовок: %s', implode(' | ', $headers))
            );
        }

        $importData = [];

        $rowCount = 0;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            ++$rowCount;

            if ($showParsingLog) {
                $io->writeln(
                    sprintf('Строка %d: %s', $rowCount, implode(' | ', $row))
                );
            }

            $importData[] = [
                self::FIELD_ID => (int) $row[0],
                self::FIELD_CODE => $row[1],
                self::FIELD_NAME => $row[2],
                self::FIELD_DATE_CREATED => $row[3],
            ];
        }

        fclose($handle);

        if (!$rowCount) {
            $io->error('Не найдены строки в файле для парсинга.');

            return Command::FAILURE;
        }

        $io->title('Запись в БД...');

        $batchSize = 500;
        $totalRecords = count($importData);

        $io->progressStart($totalRecords);

        if (!$isFake) {
            $this->em->beginTransaction();
        }

        try {
            if (!$isFake) {
                $this->em->getConnection()->getConfiguration()
                    ->setMiddlewares([new \Doctrine\DBAL\Logging\Middleware(new \Psr\Log\NullLogger())]);
            }

            for ($i = 0; $i < $totalRecords; ++$i) {
                $entity = new SourceProductType();
                $entity->setId($importData[$i][self::FIELD_ID]);
                $entity->setCode($importData[$i][self::FIELD_CODE]);
                $entity->setName($importData[$i][self::FIELD_NAME]);
                $entity->setDateCreated(new \DateTimeImmutable($importData[$i][self::FIELD_DATE_CREATED]));

                if (!$isFake) {
                    $this->em->persist($entity);
                }

                if (($i % $batchSize) === 0) {
                    if (!$isFake) {
                        $this->em->flush();
                        $this->em->clear();
                    }
                }

                ++$added;
                $io->progressAdvance();
            }

            if (!$isFake) {
                $this->em->flush();
                $this->em->clear();
                $this->em->commit();
            }
        } catch (\Throwable $e) {
            if (!$isFake) {
                $this->em->rollback();
            }

            $io->error('Ошибка при записи в БД: '.$e->getMessage());

            return Command::FAILURE;
        }

        $io->progressFinish();

        $io->title('Завершение импорта типов источников товаров');
        $io->section('Добавлено: '.$added);

        if ($isFake) {
            $io->warning('Фейковый запуск команды');
        }

        return Command::SUCCESS;
    }
}
