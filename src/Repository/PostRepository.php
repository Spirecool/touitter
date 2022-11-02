<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedPostException;
use Symfony\Component\Security\Core\Post\PasswordAuthenticatedPostInterface;
use Symfony\Component\Security\Core\Post\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
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

    /**
     * Used to upgrade (rehash) the post's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedPostInterface $post, string $newHashedPassword): void
    {
        if (!$post instanceof Post) {
            throw new UnsupportedPostException(sprintf('Instances of "%s" are not supported.', \get_class($post)));
        }

        $post->setPassword($newHashedPassword);

        $this->save($post, true);
    }

}
