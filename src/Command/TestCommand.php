<?php

namespace App\Command;

use App\Logic\ObjectChampions;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test',
    description: 'A command to test.',
)]
class TestCommand extends Command
{



    public function __construct(private ObjectChampions $objectChampions)
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
       $names = $this->objectChampions->getChampionNames();

       $result  = array_map(function ($label, $value){
          return ['label' => $label, 'value' => $value];
       }, $names,  $names);

       $json  = json_encode($result);
       dump($json);
        return Command::SUCCESS;
    }
}
