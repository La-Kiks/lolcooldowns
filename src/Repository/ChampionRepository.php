<?php

namespace App\Repository;

use App\Entity\Champion;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Champion>
 */
class ChampionRepository extends ServiceEntityRepository
{
        public function __construct(ManagerRegistry $registry,
        private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Champion::class);
    }

    public function findBySearch(SearchData $searchData): PaginationInterface
    {
        $qb = $this->createQueryBuilder('c')
        ->orderBy('c.name', 'ASC');

        if(!empty($searchData->championName )){
            $qb = $qb
                ->where('c.name LIKE :championName')
                ->setParameter('championName', $searchData->championName);

//            if(!empty($searchData->haste)){
//                $multiplier = $this->cooldownReduction($searchData->haste);
//
//                $qb = $qb
//                    ->select('c', 's.cooldowns * :multiplier AS multipliedCooldowns')
//                    ->leftJoin('c.spells', 's')
//                    ->setParameter('multiplier', $multiplier)
//                ;
//
//            }

        }
        return $this->paginator->paginate($qb, $searchData->page, 10);
    }

    /**
     * @param int $haste is the value of haste.
     * @return float cooldown reduction multiplier.
     *
     * For exemple with 100 haste : 100 / (100 + 100) = 0.5
     */
    private function cooldownReduction(int $haste): float
{
    // reduced cooldown = base cooldown x 100 / (100 + haste)
    // cooldown reduction = (haste / (haste + 100)) x 100

    return ($haste / ($haste + 100));
}
}
