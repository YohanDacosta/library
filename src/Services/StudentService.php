<?php

namespace App\Services;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

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

    public function getStudentById(int $id): ?Student
    {
        return $this->entityManager->getRepository(Student::class)->find($id);
    }

    public function filterStudentByName($filter = null): Pagerfanta
    {
        return $this->studentRepository->filterStudentByName($filter);
    }
}
