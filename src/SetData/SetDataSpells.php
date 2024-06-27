<?php

namespace App\SetData;

use App\Entity\Spell;
use App\Logic\ObjectChampions;
use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SetDataSpells

{
    public function __construct(
        private readonly ChampionRepository $championRepository,
        private readonly SpellRepository $spellRepository,
        private readonly ObjectChampions $objectChampions,
        private readonly EntityManagerInterface $manager
    )
    {

    }
    public function load(): void
    {
        $dataChampions = $this->objectChampions->getChampionsData();

        foreach ( $dataChampions as $dataChamp){
            $champion = $this->championRepository->findOneBy(['name' => $dataChamp['name']]);

            foreach($dataChamp['abilities'] as $ability => $content ){
                // Don't want to store passive in the DB at the moment
                if($ability !== 'P'){
                    $spell = $this->spellRepository->findOneBy(['champion' =>  $champion, 'name' => $ability ]);
                    if(!$spell)
                    {
                        $spell = new Spell();
                    }

                    $spell
                        ->setChampion($champion)
                        ->setName($ability)
                        ->setIcon($content['icon'])
                        ->setCooldowns($content['cooldown'])
                        ->setAffectedByCdr($content['affectedByCdr'])
                    ;
                    $this->manager->persist($spell);
                    $champion->addSpell($spell);
                }
            }
            $this->manager->flush();
        }

    }
}