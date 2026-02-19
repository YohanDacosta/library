<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Entity\LoanIteam;
use App\Enums\BookStatusEnum;
use App\Services\BookService;
use App\Services\LoanService;
use App\Services\StudentService;
use App\Services\TutorService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoanController extends AbstractController
{
    private LoanService $loanService;
    private BookService $bookService;
    private StudentService $studentService;
    private TutorService $tutorService;
    private ValidatorInterface $validator;
    public function __construct(LoanService $loanService, BookService $bookService, StudentService $studentService, TutorService $tutorService, ValidatorInterface $validator)
    {
        $this->loanService = $loanService;
        $this->bookService = $bookService;
        $this->studentService = $studentService;
        $this->tutorService = $tutorService;
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

    /**
     * @throws Exception
     */
    #[Route("/loan/create", name: "app_loan_create", methods: ["POST"])]
    public function createLoan(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Datos inválidos', Response::HTTP_BAD_REQUEST]);
        }

        try {
            $student = $this->studentService->getStudentById($data["studentId"]);
            $tutor = $this->tutorService->getTutorById($data["tutorId"]);

            $loan = new Loan();
            $loan->setStudent($student);
            $loan->setTutor($tutor);
            $loan->setLoanDate(new \DateTimeImmutable($data["loanDate"]));
            $loan->setReturnDate(new \DateTimeImmutable($data["returnDate"]));

            foreach ($data["bookIds"] as $bookId) {
                $book = $this->bookService->getBookById($bookId);
                $loanItemBook = new LoanIteam();
                $loanItemBook->setBook($book);
                $loan->addLoanIteam($loanItemBook);
                $book->setStatus(BookStatusEnum::LOANED);
            }

            $loan->setStudent($student);
            $loan->setTutor($tutor);

            $errors = $this->validator->validate($loan);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                return new JsonResponse(['error' => $errorsString, Response::HTTP_BAD_REQUEST]);
            }

            $this->loanService->createLoan($loan);

            return new JsonResponse(['success' => true, 'message' => 'Préstamo creado', Response::HTTP_CREATED]);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR]);
        }
    }

    #[Route("/loan/update", name: "app_loan_update", methods: ["PUT"])]
    public function updateLoan(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Datos inválidos', Response::HTTP_BAD_REQUEST]);
        }



        return new JsonResponse(['success' => true, 'message' => 'Préstamo creado', Response::HTTP_CREATED]);
    }
}
