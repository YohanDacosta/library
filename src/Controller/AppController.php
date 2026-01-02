<?php

namespace App\Controller;

use App\Services\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController
{
    private const COLORED_CATEGORIES = [
        'available' => 'bg-green-300',
        'loaned' => 'bg-gray-700',
        'reserved' => 'bg-blue-700',
        'repaired' => 'bg-red-300',
        'lost' => 'bg-red-700',
    ];
    private BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    #[Route('/', name: 'app_app')]
    public function index(): Response
    {
        $books = $this->bookService->getBooks();

        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
            'books' => $books,
            'categories' => self::COLORED_CATEGORIES
        ]);
    }
}
