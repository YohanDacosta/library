<?php

namespace App\Entity;

use App\Repository\TutorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TutorRepository::class)]
class Tutor
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Course>
     */
    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'tutor')]
    private Collection $course;

    /**
     * @var Collection<int, School>
     */
    #[ORM\OneToMany(targetEntity: School::class, mappedBy: 'tutor')]
    private Collection $school;

    public function __construct()
    {
        $this->course = new ArrayCollection();
        $this->school = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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
     * @return Collection<int, Course>
     */
    public function getCourse(): Collection
    {
        return $this->course;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->course->contains($course)) {
            $this->course->add($course);
            $course->setTutor($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->course->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getTutor() === $this) {
                $course->setTutor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, School>
     */
    public function getSchool(): Collection
    {
        return $this->school;
    }

    public function addSchool(School $school): static
    {
        if (!$this->school->contains($school)) {
            $this->school->add($school);
            $school->setTutor($this);
        }

        return $this;
    }

    public function removeSchool(School $school): static
    {
        if ($this->school->removeElement($school)) {
            // set the owning side to null (unless already changed)
            if ($school->getTutor() === $this) {
                $school->setTutor(null);
            }
        }

        return $this;
    }
}
