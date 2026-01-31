<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @extends ServiceEntityRepository<Student>
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function findAllStudents(): Pagerfanta
    {
        $query = $this->createQueryBuilder('s')
            ->orderBy('s.first_name', 'DESC')
            ->getQuery();
        return New Pagerfanta(new QueryAdapter($query));
    }

    public function filterStudentByName($filter): Pagerfanta
    {
        if ($filter !== "") {
            $query = $this->createQueryBuilder('s')
                ->where('s.first_name LIKE :filter')
                ->setParameter('filter', "%{$filter}%")
                ->getQuery();

            return New Pagerfanta(new QueryAdapter($query));
        }

        return New Pagerfanta(new ArrayAdapter([]));
    }

    //    /**
    //     * @return Student[] Returns an array of Student objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Student
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
