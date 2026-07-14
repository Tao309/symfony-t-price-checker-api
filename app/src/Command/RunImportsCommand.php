<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'import:run-all',
    description: 'Запуск всех команд по импорту',
)]
class RunImportsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('show-parsing-log', null, InputArgument::OPTIONAL, 'Показывать детали парсинга?', 1)
            ->addOption('fake', null, InputArgument::OPTIONAL, 'Фейковый запрос без записи?', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $showParsingLog = (bool) $input->getOption('show-parsing-log') ?? 1;
        $isFake = (bool) $input->getOption('fake') ?? 1;

        $commandNames = [
            'import:book_series',
            'import:book_publishing_brand',
            'import:book_publishing_house',
            'import:source_product_types',
            'import:book',
        ];

        try {
            foreach ($commandNames as $commandName) {
                $command = $this->getApplication()->find($commandName);

                if (!$command) {
                    throw new \RuntimeException(sprintf('Команда "%s" не найдена', $commandName));
                }

                $io->info(sprintf('Запуск команды %s', $commandName));

                $arguments = [
                    'command' => $commandName,
                    '--show-parsing-log' => $showParsingLog,
                    '--fake' => $isFake,
                ];

                $subCommandInput = new ArrayInput($arguments);

                $returnCode = $command->run($subCommandInput, $output);

                if (Command::SUCCESS === $returnCode) {
                    $io->success(sprintf('Команда "%s" успешна завершена', $commandName));
                    continue;
                }

                throw new \RuntimeException(sprintf('Команда "%s" завершена с ошибкой', $commandName));
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
