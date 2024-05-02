<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Retrieves a paginated list of items associated with a specific customer.
     *
     * This method allows effective pagination on the results obtained
     * for a given customer. It uses the QueryBuilder to build the query, 
     * filter results by client, and apply pagination constraints.
     *
     * @param Client $client The instance of the Customer entity for which the items should be retrieved.
     * @param int $page The current page number (starts at 1).
     * @param int $limit The maximum number of elements to display per page.
     *
     * @return array Items found for the specified client, limited to the requested page.
     *               Each page returns an array of entities matching the criteria.
     *
     */
    public function findByClientWithPagination(Client $client, $page, $limit)
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.client = :client')
            ->setParameter('client', $client)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
    
        return $qb->getQuery()->getResult();
    }

    public function getNumberOfPagesByClientWithPagination(Client $client, $page, $limit)
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->where('b.client = :client')
            ->setParameter('client', $client)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
    
        $results = $qb->getQuery()->getResult();
    
        return $qb->select('COUNT(b.id)')->getQuery()->getSingleScalarResult();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
