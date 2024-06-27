<?php

namespace App\SetData;

use App\Entity\Champion;
use App\Logic\ObjectChampions;
use App\Repository\ChampionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SetDataChampions

{
    public function __construct(
        private readonly ChampionRepository $championRepository,
        private readonly ObjectChampions $objectChampions,
        private readonly EntityManagerInterface $manager
    )
    {

    }
    public function load(): void
    {
        $dataChampions = $this->objectChampions->getChampionsData();

        foreach ( $dataChampions as $dataChamp){
            $champion = $this->championRepository->findOneBy(['key' => $dataChamp['key']]);
            if(!$champion)
            {
                $champion = new Champion();
            }
            $champion
                ->setKey($dataChamp['key'])
                ->setName($dataChamp['name'])
                ->setIcon($dataChamp['icon'])
            ;
            $this->manager->persist($champion);

        }
        $this->manager->flush();
    }
}