<?php

namespace App\Command;

use App\Entity\Champion;
use App\Logic\ObjectChampions;
use App\SetData\SetDataChampions;
use App\SetData\SetDataSpells;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:champions:load:database',
    description: 'Get the data from the custom JSON public/champions.json to fill the DB.',
    aliases: ['app:load-db']
)]
class LoadChampionsDataCommand extends Command
{
    public function __construct(private readonly SetDataChampions       $setDataChampions,
                                private readonly SetDataSpells          $setDataSpells,
                                private readonly ObjectChampions        $objectChampions,
                                private readonly EntityManagerInterface $em)
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

        $this->setDataChampions->load();
        $this->setDataSpells->load();

        return Command::SUCCESS;
    }
}
