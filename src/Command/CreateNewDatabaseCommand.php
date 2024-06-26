<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:champions:create:new:database',
    description: 'Remove the old database & create a new one',
    aliases: ['app:create-db']
)]
class CreateNewDatabaseCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commands = [
            'php bin/console doctrine:database:drop --force',
            'php bin/console d:d:c',
            'php bin/console make:migration',
            'php bin/console d:m:m --no-interaction',
        ];

        foreach ($commands as $command) {
            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output->writeln(sprintf('<info>Command "%s" executed successfully</info>', $command));
        }
        return Command::SUCCESS;
    }
}
