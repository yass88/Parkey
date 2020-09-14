<?php

namespace App\Repository;

use App\Entity\Parking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parking[]    findAll()
 * @method Parking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParkingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parking::class);
    }

    /**
     * @return Parking[] Returns an array of Post objects
     * @param $title
     * @param $largeur
     * @param $longueur
     * @param $hauteur
     * @param $guard
     * @param $camera
     * @param $covered
     * @param $locked
     */

    public function research($title, $largeur, $longueur, $hauteur, $guard, $camera, $covered, $locked)
    {
        #dd($address, $title);
        #dd($title->getParkings());
        return $this->createQueryBuilder('p')

            ->andWhere('p.category = :category')
            ->setParameter('category', $title)

            ->andWhere('p.largeur = :largeur')
            ->setParameter('largeur', $largeur)

            ->andWhere('p.longueur = :longueur')
            ->setParameter('longueur', $longueur)

            ->andWhere('p.hauteur = :hauteur')
            ->setParameter('hauteur', $hauteur)

            ->andWhere('p.guard = :guard')
            ->setParameter('guard', $guard)

            ->andWhere('p.camera = :camera')
            ->setParameter('camera', $camera)

            ->andWhere('p.covered = :covered')
            ->setParameter('covered', $covered)

            ->andWhere('p.locked = :locked')
            ->setParameter('locked', $locked)

// date
//            ->andWhere('p.availability_end = :availability_end')
//            ->setParameter('availability_end', $availability_end)

            ->orderBy('p.id', 'ASC')
            ->setMaxResults(16)
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?Parking
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
