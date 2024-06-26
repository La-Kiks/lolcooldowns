<?php

namespace App\SetData;

use App\Entity\Champion;
use App\Logic\ObjectChampions;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SetDataChampions

{
    public function __construct(private readonly LoggerInterface $logger,
                                private readonly string $publicDir)
    {

    }
    public function load(ObjectChampions $objectChampions, EntityManagerInterface $manager): void
    {
        $commands = [
            'php bin/console d:d:d --force',
            'php bin/console d:d:c',
            'php bin/console make:migration',
            'php bin/console d:m:m --no-interaction',
        ];
        $dataChampions = $objectChampions->getChampionsData();

        foreach ( $dataChampions as $dataChamp){
            $champion = new Champion();
            $champion
                ->setKey($dataChamp['key'])
                ->setName($dataChamp['name'])
                ->setIcon($dataChamp['icon'])
            ;
            $manager->persist($champion);


        }
        $manager->flush();
    }
}