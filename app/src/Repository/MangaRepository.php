<?php

namespace App\Repository;

use App\Entity\Manga;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Manga>
 */
class MangaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manga::class);
    }
    
    /**
     * Recherche de mangas avec filtres
     */
    public function findBySearch(array $criteria, User $owner): array
    {
        $qb = $this->createQueryBuilder('m')
            ->join('m.category', 'c')
            ->leftJoin('m.Tags', 't')
            ->where('m.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('m.title', 'ASC');
            
        if (!empty($criteria['title'])) {
            $qb->andWhere('m.title LIKE :title')
               ->setParameter('title', '%' . $criteria['title'] . '%');
        }
        
        if (!empty($criteria['minPrice'])) {
            $qb->andWhere('m.price >= :minPrice')
               ->setParameter('minPrice', $criteria['minPrice']);
        }
        
        if (!empty($criteria['maxPrice'])) {
            $qb->andWhere('m.price <= :maxPrice')
               ->setParameter('maxPrice', $criteria['maxPrice']);
        }
        
        if (!empty($criteria['category'])) {
            $qb->andWhere('m.category = :category')
               ->setParameter('category', $criteria['category']);
        }
        
        if (!empty($criteria['tags'])) {
            $qb->andWhere('t.id IN (:tags)')
               ->setParameter('tags', $criteria['tags']);
        }
        
        return $qb->getQuery()->getResult();
    }
}