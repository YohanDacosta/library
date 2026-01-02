<?php

namespace App\Services;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getBooks(): array
    {
        return $this->entityManager->getRepository(Book::class)->findAll();
    }

    public function getBookById(int $id): ?Book
    {
        return $this->entityManager->getRepository(Book::class)->find($id);
    }
}
