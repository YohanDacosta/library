<?php

namespace App\Services;

use Exception;
use App\Entity\Loan;
use App\Enums\BookStatusEnum;
use App\Enums\LoanStatusEnum;
use App\Repository\LoanRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;

class LoanService
{
    private EntityManagerInterface $entityManager;
    private LoanRepository $loanRepository;
    public function __construct(EntityManagerInterface $entityManager, LoanRepository $loanRepository)
    {
        $this->entityManager = $entityManager;
        $this->loanRepository = $loanRepository;
    }

    public function getLoans(): Pagerfanta
    {
        return $this->loanRepository->findAllLoans();
    }

    public function getLoanById(Uuid $id): ?Loan
    {
        return $this->entityManager->getRepository(Loan::class)->find($id);
    }

    public function filterLoanByName($filter = null): Pagerfanta
    {
        return $this->loanRepository->filterLoanByNameBook($filter);
    }

    public function createLoan($data): void
    {
        $this->loanRepository->createLoan($data);
        $this->entityManager->flush();
    }

    /**
     * @throws Exception
     */
    public function updateLoan(
        Uuid $loanId,
        LoanStatusEnum $status,
        string $returnDate,
        array $books): bool
    {
        $loan = $this->loanRepository->getLoanById($loanId);

        if (!$loan) {
            return false;
        }
        try {
            $listBookIds = [];
            foreach ($books as $book) {
                $listBookIds[$book['bookId']] = $book['status'];
            }
            foreach ($loan->getLoanIteams() as $loanItem) {
                $bookId = $loanItem->getBook()->getId()->toRfc4122();
                if (isset($listBookIds[$bookId])) {
                    if ($loanItem->getBook()->getStatus()->value != $listBookIds[$bookId]) {
                        $loanItem->getBook()->setStatus(BookStatusEnum::tryFrom($listBookIds[$bookId]));
                        $loanItem->getBook()->setUpdatedAt(new \DateTimeImmutable());
                    }
                }
            }
            $loan->setStatus($status);

            if ($returnDate) {
                $loan->setReturnDate(new \DateTimeImmutable($returnDate));
            }
            $loan->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}
