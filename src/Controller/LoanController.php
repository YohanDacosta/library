<?php

namespace App\Controller;

use Exception;
use App\Entity\Loan;
use App\Entity\LoanIteam;
use App\Enums\BookStatusEnum;
use App\Enums\LoanStatusEnum;
use App\Services\BookService;
use App\Services\LoanService;
use App\Services\TutorService;
use App\Services\StudentService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoanController extends AbstractController
{
    private LoanService $loanService;
    private BookService $bookService;
    private StudentService $studentService;
    private TutorService $tutorService;
    private ValidatorInterface $validator;

    private LoggerInterface $logger;

    private CsrfTokenManagerInterface $csrfTokenManager;
    public function __construct(
        LoanService $loanService,
        BookService $bookService,
        StudentService $studentService,
        TutorService $tutorService,
        ValidatorInterface $validator,
        CsrfTokenManagerInterface $csrfTokenManager,
        LoggerInterface $logger
    )
    {
        $this->loanService = $loanService;
        $this->bookService = $bookService;
        $this->studentService = $studentService;
        $this->tutorService = $tutorService;
        $this->validator = $validator;
        $this->csrfTokenManager = $csrfTokenManager;
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
        $loans->setMaxPerPage(24);
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
        $csrfToken = $request->headers->get('x-csrf-token');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('loan_form', $csrfToken))) {
            return new JsonResponse(['error' => 'Invalid CSRF token', Response::HTTP_FORBIDDEN]);
        }
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Datos inválidos', Response::HTTP_BAD_REQUEST]);
        }

        try {
            $loan = new Loan();

            if (!$data["isSelfLoan"]) {
                $student = $this->studentService->getStudentById($data["studentId"]);

                if (!$student) {
                    return new JsonResponse(['error' => 'Estudiante no encontrado', Response::HTTP_NOT_FOUND]);
                }
                $loan->setStudent($student);
            }
            $tutor = $this->tutorService->getTutorById($data["tutorId"]);

            if (!$tutor) {
                return new JsonResponse(['error' => 'Tutor no encontrado', Response::HTTP_NOT_FOUND]);
            }

            $loan->setTutor($tutor);
            $loan->setLoanDate(\DateTimeImmutable::createFromFormat('Y-m-d', $data["loanDate"]));
            $loan->setReturnDate(\DateTimeImmutable::createFromFormat('Y-m-d', $data["returnDate"]));

            foreach ($data["bookIds"] as $bookId) {
                $book = $this->bookService->getBookById($bookId);
                if ($book) {
                    $loanItemBook = new LoanIteam();
                    $loanItemBook->setBook($book);
                    $loan->addLoanIteam($loanItemBook);
                    $book->setStatus(BookStatusEnum::LOANED);
                }
            }

            $loan->setTutor($tutor);

            $errors = $this->validator->validate($loan);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                return new JsonResponse(['error' => $errorsString, Response::HTTP_BAD_REQUEST]);
            }

            $this->loanService->createLoan($loan);

            return new JsonResponse(['success' => true, 'message' => 'Préstamo creado', Response::HTTP_CREATED]);
        } catch (Exception $e) {
            $this->logger->error(
                'Error al procesar la solicitud',
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
            return new JsonResponse(['error' => 'Error al procesar la solicitud', Response::HTTP_INTERNAL_SERVER_ERROR]);
        }
    }

    /**
     * @throws Exception
     */
    #[Route("/loan/update", name: "app_loan_update", methods: ["PUT"])]
    public function updateLoan(Request $request): JsonResponse
    {
        $csrfToken = $request->headers->get('x-csrf-token');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('loan_form', $csrfToken))) {
            return new JsonResponse('Invalid CSRF token', Response::HTTP_FORBIDDEN);
        }
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Datos inválidos', Response::HTTP_BAD_REQUEST]);
        }

        $result = $this->loanService->updateLoan(
            Uuid::fromString($data['loanId']),
            LoanStatusEnum::tryFrom($data['status']),
            $data['returnDate'],
            $data['books']);

        if (!$result) {
            return new JsonResponse(['error' => 'Not Found', Response::HTTP_NOT_FOUND]);
        }
        return new JsonResponse(['success' => true, 'message' => 'Préstamo actualizado', Response::HTTP_OK]);
    }
}
