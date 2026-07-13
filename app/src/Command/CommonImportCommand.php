<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

abstract class CommonImportCommand extends Command
{
    protected ?SymfonyStyle $io = null;
    protected int $added = 0;
    protected int $showParsingLog = 1;
    protected int $isFake = 1;
    protected array $importData = [];
    protected string $filePath = '';

    abstract protected function fillImportRow(array $row): void;

    abstract protected function createEntityByImportRowData(array $row): mixed;

    public function __construct(
        #[Autowire('%kernel.project_dir%')] protected readonly string $projectDir,
        protected readonly EntityManagerInterface $em,
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
        $this->io = new SymfonyStyle($input, $output);
        $this->added = 0;
        $this->showParsingLog = (bool) $input->getOption('show-parsing-log') ?? 1;
        $this->isFake = (bool) $input->getOption('fake') ?? 1;

        try {
            $this->assembleImportData();
            $this->persistImportData();
        } catch (\Throwable $e) {
            $this->io->error($e->getMessage());

            return Command::FAILURE;
        }

        $this->io->title('Завершение импорта: '.static::COMMAND_LABEL);
        $this->io->section('Добавлено: '.$this->added);

        if ($this->isFake) {
            $this->io->warning('Фейковый запуск команды');
        }

        return Command::SUCCESS;
    }

    protected function assembleImportData(): void
    {
        $io = $this->io;

        $io->title('Начало импорта: '.static::COMMAND_LABEL);
        if ($this->isFake) {
            $io->warning('Фейковый запуск команды');
        }

        $io->section('Поиск данных для импорта из файла');
        $filePath = $this->projectDir.$this->filePath;

        if (empty($this->filePath) || !file_exists($filePath) || !is_readable($filePath)) {
            throw new \RuntimeException(sprintf('Файл "%s" не найден или недоступен к чтению.', $filePath));
        }

        if (($handle = fopen($filePath, 'r')) === false) {
            throw new \RuntimeException('Ошибка при открытии файла.');
        }

        $io->title('Парсинг CSV файла...');

        $headers = fgetcsv($handle, 0, ',');
        if ($this->showParsingLog) {
            $io->writeln(
                sprintf('Заголовок: %s', implode(' | ', $headers))
            );
        }

        $rowCount = 0;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            ++$rowCount;

            if ($this->showParsingLog) {
                $io->writeln(
                    sprintf('Строка %d: %s', $rowCount, implode(' | ', $row))
                );
            }

            $this->fillImportRow($row);
        }

        fclose($handle);

        if (!$rowCount) {
            throw new \RuntimeException('Не найдены строки в файле для парсинга.');
        }
    }

    protected function persistImportData(): void
    {
        $this->io->title('Запись в БД...');

        $batchSize = 500;
        $totalRecords = count($this->importData);

        $this->io->progressStart($totalRecords);

        if (!$this->isFake) {
            $this->em->beginTransaction();
        }

        try {
            if (!$this->isFake) {
                $this->em->getConnection()->getConfiguration()
                    ->setMiddlewares([new \Doctrine\DBAL\Logging\Middleware(new \Psr\Log\NullLogger())]);
            }

            for ($i = 0; $i < $totalRecords; ++$i) {
                $entity = $this->createEntityByImportRowData($this->importData[$i]);

                if (!$this->isFake) {
                    $this->em->persist($entity);
                }

                if (($i % $batchSize) === 0) {
                    if (!$this->isFake) {
                        $this->em->flush();
                        $this->em->clear();
                    }
                }

                ++$this->added;
                $this->io->progressAdvance();
            }

            if (!$this->isFake) {
                $this->em->flush();
                $this->em->clear();
                $this->em->commit();
            }
        } catch (\Throwable $e) {
            if (!$this->isFake) {
                $this->em->rollback();
            }

            throw new \RuntimeException('Ошибка при записи в БД: '.$e->getMessage());
        }

        $this->io->progressFinish();
    }
}
