<?php

namespace App\Command;

use App\Logic\Champions;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'app:champions',
    description: 'Add a short description for your command',
)]
class ChampionsCommand extends Command
{
    public function __construct(private Champions $champions)
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->champions->championsCustom();

        return Command::SUCCESS;
    }
}