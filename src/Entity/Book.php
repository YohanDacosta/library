<?php

namespace App\Entity;

use App\Enums\BookStatusEnum;
use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Random\RandomException;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[UniqueEntity('code', message: 'Code already exists.')]
class Book
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid|null $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 100)]
    private ?string $author = null;

    #[ORM\Column(enumType: BookStatusEnum::class)]
    private ?BookStatusEnum $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $isbn = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, BookCategory>
     */
    #[ORM\ManyToMany(targetEntity: BookCategory::class, inversedBy: 'books')]
    private Collection $categories;

    /**
     * @var Collection<int, LoanIteam>
     */
    #[ORM\OneToMany(targetEntity: LoanIteam::class, mappedBy: 'book', orphanRemoval: true)]
    private Collection $loanIteams;

    #[ORM\Column(nullable: true)]
    public ?string $image;

    /**
     * @throws RandomException
     */
    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->created_at = new \DateTimeImmutable();
        $this->categories = new ArrayCollection();
        $this->code = time() . random_int(100, 999);
        $this->status = BookStatusEnum::AVAILABLE;
        $this->loanIteams = new ArrayCollection();
    }

    public function getId(): Uuid|null
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getStatus(): ?BookStatusEnum
    {
        return $this->status;
    }

    public function setStatus(BookStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getImageUrl(): ?string
    {
        if (!$this->image) {
            return '/images/default.jpg';
        }

        if (str_contains($this->image, '/')) {
            return $this->image;
        }
        return sprintf('/uploads/books/%s', $this->image);
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

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

    /**
     * @return Collection<int, BookCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(BookCategory $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(BookCategory $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function __toString(): string
    {
        return $this->title ?? '';
    }

    /**
     * @return Collection<int, LoanIteam>
     */
    public function getLoanIteams(): Collection
    {
        return $this->loanIteams;
    }

    public function addLoanIteam(LoanIteam $loanIteam): static
    {
        if (!$this->loanIteams->contains($loanIteam)) {
            $this->loanIteams->add($loanIteam);
            $loanIteam->setBook($this);
        }

        return $this;
    }

    public function removeLoanIteam(LoanIteam $loanIteam): static
    {
        if ($this->loanIteams->removeElement($loanIteam)) {
            // set the owning side to null (unless already changed)
            if ($loanIteam->getBook() === $this) {
                $loanIteam->setBook(null);
            }
        }

        return $this;
    }
}
