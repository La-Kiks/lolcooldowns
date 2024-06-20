<?php

namespace App\SetData;

use App\Entity\Spell;
use App\Logic\ObjectChampions;
use App\Repository\ChampionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SetDataSpells

{
    public function __construct(private readonly LoggerInterface $logger,
                                private readonly string $publicDir,
                                private readonly ChampionRepository $championRepository)
    {

    }
    public function load(ObjectChampions $objectChampions, EntityManagerInterface $manager): void
    {
        $dataChampions = $objectChampions->getChampionsData();

        foreach ( $dataChampions as $dataChamp){
            $champion = $this->championRepository->findBy(['name' => $dataChamp['name']]);

            foreach($dataChamp['abilities'] as $ability => $content ){

                // Don't want to store passive in the DB at the moment
                if($ability !== 'P'){
                    $spell = new Spell();
                    $spell
                        ->setChampion($champion[0])
                        ->setName($ability)
                        ->setIcon($content['icon'])
                        ->setCooldowns($content['cooldown'])
                        ->setAffectedByCdr($content['affectedByCdr'])
                    ;
                    $manager->persist($spell);
                    $champion[0]->addSpell($spell);
                }
            }
            $manager->flush();
        }

    }
}