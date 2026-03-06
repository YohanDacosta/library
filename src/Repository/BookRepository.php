<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findAllBooks(array $categories = [], ?string $sort = null): Pagerfanta
    {
        $query = $this->createQueryBuilder('b');

        if ($sort === "oldest") {
            $query->orderBy('b.created_at', 'ASC');
        } else {
            $query->orderBy('b.created_at', 'DESC');
        }

        if (!empty($categories)) {
            $arrayCategoriesUuid = array_map(fn($id) => Uuid::fromString($id)->toBinary(), $categories);
            $query->distinct()
                ->innerJoin('b.categories', 'c')
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $arrayCategoriesUuid, ArrayParameterType::BINARY);
        }
        return new Pagerfanta(new QueryAdapter($query));
    }

    public function filterBookByTitle($filter): Pagerfanta
    {
        if ($filter !== "") {
            $query = $this->createQueryBuilder('b')
                ->where('b.title LIKE :filter')
                ->setParameter('filter', "%{$filter}%")
                ->getQuery();
            return new Pagerfanta(new QueryAdapter($query));
        }

        return new Pagerfanta(new ArrayAdapter([]));
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
