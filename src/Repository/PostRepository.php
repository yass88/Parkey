<?php

namespace App\Repository;

use App\Controller\SearchController;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }


    /**
     * @param $address
     * @param $title
     * @param \DateTime $availability
     * @param \DateTime $availability_end
     * @return \Doctrine\ORM\QueryBuilder Returns an array of Post objects
     */

    public function research($address, \DateTime $availability, \DateTime $availability_end)
    {

        $query = $this->createQueryBuilder('p')
            ->andWhere('p.address LIKE :address')
            ->setParameter('address', '%' . $address . '%');

        $query
            ->andWhere('p.availability >= :availability')
            ->setParameter('availability', $availability)
            ->andWhere('p.availability_end <= :availability_end')
            ->setParameter('availability_end', $availability_end);


        return $query
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByAddress($address)
    {

        $query = $this->createQueryBuilder('p')
            ->andWhere('p.address LIKE :address')
            ->setParameter('address', '%' . $address . '%');

        return $query
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
