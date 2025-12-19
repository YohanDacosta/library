<?php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
class School
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Tutor>
     */
    #[ORM\ManyToMany(targetEntity: Tutor::class, mappedBy: 'schools')]
    private Collection $tutors;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->tutors = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Tutor>
     */
    public function getTutors(): Collection
    {
        return $this->tutors;
    }

    public function addTutor(Tutor $tutor): static
    {
        if (!$this->tutors->contains($tutor)) {
            $this->tutors->add($tutor);
            $tutor->addSchool($this);
        }

        return $this;
    }

    public function removeTutor(Tutor $tutor): static
    {
        if ($this->tutors->removeElement($tutor)) {
            $tutor->removeSchool($this);
        }

        return $this;
    }
}
