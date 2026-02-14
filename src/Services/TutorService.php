<?php

namespace App\Services;

use App\Repository\TutorRepository;

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
}
