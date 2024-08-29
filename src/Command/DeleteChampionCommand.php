<?php

namespace App\Command;

use App\Repository\ChampionRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:champions:delete',
    description: 'Delete one specific entry from the DB',
    aliases: ['app:champ-delete']
)]
class DeleteChampionCommand extends Command
{
    public function __construct(private readonly ChampionRepository       $championRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    /**
     * Set Data functions works to fill an empty DB.
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = 'Aurora';
        $this->championRepository->deleteChampionByName($name);

        return Command::SUCCESS;
    }
}
