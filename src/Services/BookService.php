<?php

namespace App\Services;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

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

    public function getBookById(int $id): ?Book
    {
        return $this->entityManager->getRepository(Book::class)->find($id);
    }

    public function filterBookByName($filter = null): Pagerfanta
    {
        return $this->bookRepository->filterBookByTitle($filter);
    }
}
