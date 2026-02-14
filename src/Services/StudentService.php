<?php

namespace App\Services;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Uid\Uuid;

class StudentService
{
    private EntityManagerInterface $entityManager;
    private StudentRepository $studentRepository;
    public function __construct(EntityManagerInterface $entityManager, StudentRepository $studentRepository)
    {
        $this->entityManager = $entityManager;
        $this->studentRepository = $studentRepository;
    }

    public function getStudents(): Pagerfanta
    {
        return $this->studentRepository->findAllStudents();
    }

    public function getStudentsByTutor(Uuid $id): array
    {
        return $this->studentRepository->findStudentByTutor($id);
    }

    public function filterStudentByName($filter = null): Pagerfanta
    {
        return $this->studentRepository->filterStudentByName($filter);
    }
}
