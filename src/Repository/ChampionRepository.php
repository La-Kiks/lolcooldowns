<?php

namespace App\Repository;

use App\Entity\Champion;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use function Doctrine\ORM\QueryBuilder;

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

        if($searchData->champions){
            $qb->andWhere('c.name IN (:champions)')
                ->setParameter('champions', array_column($searchData->champions, 'champion') );
        }

        return $this->paginator->paginate($qb, $searchData->page, 10);
    }
    
}
