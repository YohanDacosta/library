<?php

namespace App\Services;

use App\Entity\Loan;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Uid\Uuid;

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
}
