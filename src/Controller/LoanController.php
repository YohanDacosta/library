<?php

namespace App\Controller;

use App\Services\LoanService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoanController extends AbstractController
{
    private LoanService $loanService;
    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    #[Route('/loan', name: 'app_loan')]
    public function index(Request $request): Response
    {
        $searchTerm = $request->query->get('q');

        if ($request->query->get('preview')){
            $loans = $this->loanService->filterLoanByName($searchTerm);

            return $this->render('components/_loan_preview.html.twig', [
                'loans' => $loans
            ]);
        }

        $loans = $this->loanService->getLoans();
        $loans->setMaxPerPage(2);
        $loans->setCurrentPage($request->query->get('page', 1));


        return $this->render('loans/index.html.twig', [
            'controller_name' => 'LoanController',
            'loans' => $loans,
//            'categories' => self::COLORED_CATEGORIES,
            'searchTerm' => $searchTerm
        ]);
    }
}
