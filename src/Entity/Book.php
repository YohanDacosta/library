<?php

namespace App\Entity;

use App\Enums\BookStatusEnum;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: BookRepository::class)]
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

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $copies_available = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, BookCategory>
     */
    #[ORM\ManyToMany(targetEntity: BookCategory::class, inversedBy: 'books')]
    private Collection $categories;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->created_at = new \DateTimeImmutable();
        $this->status = BookStatusEnum::AVAILABLE;
        $this->categories = new ArrayCollection();
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

    public function getCopiesAvailable(): ?int
    {
        return $this->copies_available;
    }

    public function setCopiesAvailable(int $copies_available): static
    {
        $this->copies_available = $copies_available;

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
}
