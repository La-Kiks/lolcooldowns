<?php

namespace App\Command;

use App\Repository\Logic\Versions;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:versions',
    description: 'Check versions using -vv',
    aliases: ['app:v']
)]
class CheckVersionsCommand extends Command
{
    public function __construct(private Versions $versions)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
       $this->versions->compareVersionsDDMera();

        return Command::SUCCESS;
    }
}
