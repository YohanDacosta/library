<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Services\LoanService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoanController extends AbstractController
{
    private LoanService $loanService;
    private ValidatorInterface $validator;
    public function __construct(LoanService $loanService, ValidatorInterface $validator)
    {
        $this->loanService = $loanService;
        $this->validator = $validator;
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

    #[Route("/loan/create", name: "app_loan_create")]
    public function createLoan(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['success' => false, 'message' => 'Error', 'data' => null]);
        }

//        $loan = new Loan();
//        $loan->setBook($data["bookIds"]);
//        $loan->setStudent($data["studentId"]);
//        $loan->setTutor($data["tutorId"]);
//        $loan->setLoanDate($data["loanDate"]);
//        $loan->setReturnDate($data["returnDate"]);
//
//        $errors = $this->validator->validate($loan);
//
//        if (count($errors) > 0) {
//            $errorsString = (string) $errors;
//
//            return new JsonResponse(['success' => false, 'message' => $errorsString, 'data' => null]);
//        }
//
//        $this->loanService->createLoan($loan);



        return new JsonResponse(['success' => true, 'message' => 'Préstamo creado', 'data' => $data]);
    }
}
