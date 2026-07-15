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
            ->addOption('just-check-parsing', null, InputArgument::OPTIONAL, 'Проверить только парсинг?', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $showParsingLog = (bool) ($input->getOption('show-parsing-log') ?? 1);
        $isFake = (bool) ($input->getOption('fake') ?? 1);
        $justCheckParsing = (bool) ($input->getOption('just-check-parsing') ?? 1);

        $commandNames = [
            'import:book_series',
            'import:book_publishing_brands',
            'import:book_publishing_houses',
            'import:source_product_types',
            'import:books',
            'import:products',
            'import:product_prices',
            'import:product_stocks',
            'import:book_user_data',
            'import:source_products',
            'import:source_product_user_data',
            'import:product_user_data',
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
                    '--just-check-parsing' => $justCheckParsing,
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
