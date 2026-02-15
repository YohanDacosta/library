<?php

namespace App\Services;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Uid\Uuid;

class BookService
{
    private EntityManagerInterface $entityManager;
    private BookRepository $bookRepository;
    public function __construct(EntityManagerInterface $entityManager, BookRepository $bookRepository)
    {
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
    }

    public function getBooks(): Pagerfanta
    {
        return $this->bookRepository->findAllBooks();
    }

    public function getBookById(String $id): ?Book
    {
        return $this->entityManager->getRepository(Book::class)->find(Uuid::fromString($id));
    }

    public function filterBookByName($filter = null): Pagerfanta
    {
        return $this->bookRepository->filterBookByTitle($filter);
    }
}
