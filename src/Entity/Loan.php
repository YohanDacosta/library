<?php

namespace App\Entity;

use App\Enums\LoanEnum;
use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $loan_date = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $due_date = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $return_date = null;

    #[ORM\Column(enumType: LoanEnum::class)]
    private ?LoanEnum $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'loan')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\ManyToOne(inversedBy: 'loan')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'loan')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tutor $tutor = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->status = LoanEnum::ACTIVE;
        $this->loan_date = new \DateTimeImmutable();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getLoanDate(): ?\DateTimeImmutable
    {
        return $this->loan_date;
    }

    public function setLoanDate(\DateTimeImmutable $loan_date): static
    {
        $this->loan_date = $loan_date;

        return $this;
    }

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->due_date;
    }

    public function setDueDate(?\DateTimeImmutable $due_date): static
    {
        $this->due_date = $due_date;

        return $this;
    }

    public function getReturnDate(): ?\DateTimeImmutable
    {
        return $this->return_date;
    }

    public function setReturnDate(\DateTimeImmutable $return_date): static
    {
        $this->return_date = $return_date;

        return $this;
    }

    public function getStatus(): ?LoanEnum
    {
        return $this->status;
    }

    public function setStatus(LoanEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

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

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getTutor(): ?Tutor
    {
        return $this->tutor;
    }

    public function setTutor(?Tutor $tutor): static
    {
        $this->tutor = $tutor;

        return $this;
    }
}
