<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBySearch($search): array
    {
        return $this->createQueryBuilder('p')
            // ->innerJoin('p.user', 'u', 'WITH', 'u.username = :username')
            //on recherche par titre et par contenu (mais pas par utilisateur)
            ->andWhere('p.title LIKE :search OR p.content LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}

