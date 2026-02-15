<?php

namespace App\Entity;

use App\Repository\LoanIteamRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LoanIteamRepository::class)]
class LoanIteam
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid|null $id = null;

    #[ORM\ManyToOne(inversedBy: 'loanIteams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Loan $loan = null;

    #[ORM\ManyToOne(inversedBy: 'loanIteams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getLoan(): ?Loan
    {
        return $this->loan;
    }

    public function setLoan(?Loan $loan): static
    {
        $this->loan = $loan;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }
}
