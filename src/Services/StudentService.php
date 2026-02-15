<?php

namespace App\Services;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Uid\Uuid;

class StudentService
{
    private StudentRepository $studentRepository;
    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function getStudents(): Pagerfanta
    {
        return $this->studentRepository->findAllStudents();
    }

    public function getStudentById(Uuid $id): ?Student
    {
        return $this->studentRepository->find($id);
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
