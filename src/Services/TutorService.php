<?php

namespace App\Services;

use App\Entity\Tutor;
use App\Repository\TutorRepository;
use Symfony\Component\Uid\Uuid;

class TutorService
{
    private TutorRepository $tutorRepository;

    public function __construct(TutorRepository $tutorRepository)
    {
        $this->tutorRepository = $tutorRepository;
    }

    public function getTutors(): array {
        return $this->tutorRepository->findAll();
    }

    public function getTutorById(String $id): ?Tutor
    {
        return $this->tutorRepository->find(Uuid::fromString($id));
    }
}
