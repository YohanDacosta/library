<?php

namespace App\Services;

use App\Repository\BookCategoryRepository;

class BookCategoryService
{
    private BookCategoryRepository $bookCategoryRepository;
    public function __construct(BookCategoryRepository $bookCategoryRepository)
    {
        $this->bookCategoryRepository = $bookCategoryRepository;
    }

    public function getCategories(): array {
        return $this->bookCategoryRepository->findAll();
    }
}
