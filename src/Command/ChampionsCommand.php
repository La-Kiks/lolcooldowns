<?php

namespace App\Command;

use App\Logic\ObjectChampions;
use App\Logic\Versions;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'app:champions:update:json',
    description: 'Check if JSON updates are possible, then  update all the JSON.',
    aliases: ['app:update']
)]
class ChampionsCommand extends Command
{
    public function __construct(private ObjectChampions $champions,  private Versions $versions)
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    /**
     * Initial champions data is downloaded from Meraki using version logic, if "compareVersionsDDMera" = false.
     * Create a new championsMeraki.sjon via "createMerakiJSON".
     * From the championsMeraki.json we can use "findExceptionsMeraki" to generate 3 json files Abilities, CD, Recharge.
     * Finally, create the champions.json using "championsCustom" which should handle all the exceptions.
     * @throws \JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if($this->versions->compareVersionsDDMera()){
            return Command::FAILURE;
        } else {
            $this->champions->createMerakiJSON();
            $this->champions->findExceptionsMeraki();
            $this->champions->championsCustom();
            $this->versions->updateVersions();
            return Command::SUCCESS;
        }

//        $this->versions->createVersionMeraki();
//        return Command::SUCCESS;
    }
}
