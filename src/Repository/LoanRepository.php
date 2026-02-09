<?php

namespace App\Repository;

use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @extends ServiceEntityRepository<Loan>
 */
class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    public function findAllLoans(): Pagerfanta
    {
        $query = $this->createQueryBuilder('l')
            ->orderBy('l.created_at', 'DESC')
            ->getQuery();
        return New Pagerfanta(new QueryAdapter($query));
    }

    public function filterLoanByNameBook($filter): Pagerfanta
    {
        if ($filter === null || $filter === "") {
            return new Pagerfanta(new ArrayAdapter([]));
        }

        $query = $this->createQueryBuilder('l')
            ->select('DISTINCT l')
            ->join('l.student', 's')
            ->join('l.book', 'b')
            ->join('l.tutor', 't')
            ->where('s.first_name LIKE :filter')
            ->orWhere('s.last_name LIKE :filter')
            ->orWhere('b.title LIKE :filter')
            ->setParameter('filter', "%{$filter}%")
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
        return new Pagerfanta(new ArrayAdapter($query));
    }

    //    /**
    //     * @return Loan[] Returns an array of Loan objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Loan
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
