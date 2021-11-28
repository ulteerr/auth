<?php

namespace App\Repository;

use App\Entity\Cinema;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cinema|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cinema|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cinema[]    findAll()
 * @method Cinema[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CinemaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cinema::class);
    }
    public function searchByQuery(string $query)
    {
        return $this->createQueryBuilder('cinema')
            ->where('cinema.title LIKE :query')
            ->setParameter('query', '%'. $query. '%')
            ->getQuery()
            ->getResult();
    }
}
