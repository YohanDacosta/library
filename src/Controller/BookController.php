<?php

namespace App\Controller;

use App\Services\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController
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

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $searchTerm = $request->query->get('q');

        if ($request->query->get('preview')){
            $books = $this->bookService->filterBookByName($searchTerm);

            return $this->render('components/_preview.html.twig', [
                    'books' => $books
            ]);
        }

        $books = $this->bookService->getBooks();
        $books->setMaxPerPage(5);
        $books->setCurrentPage($request->query->get('page', 1));

        return $this->render('home/index.html.twig', [
            'controller_name' => 'BookController',
            'books' => $books,
            'categories' => self::COLORED_CATEGORIES,
            'searchTerm' => $searchTerm
        ]);
    }
}
