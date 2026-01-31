<?php

namespace App\Controller;

use App\Services\StudentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StudentController extends AbstractController
{
    private StudentService $studentService;
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    #[Route('/student', name: 'student_index')]
    public function index(Request $request): Response
    {
        $searchTerm = $request->query->get('q');

        if ($request->query->get('preview')){
            $students = $this->studentService->filterStudentByName($searchTerm);

            return $this->render('components/_student_preview.html.twig', [
                'students' => $students
            ]);
        }

        $students = $this->studentService->getStudents();
        $students->setMaxPerPage(2);
        $students->setCurrentPage($request->query->get('page', 1));


        return $this->render('students/index.html.twig', [
            'controller_name' => 'StudentController',
            'students' => $students,
//            'categories' => self::COLORED_CATEGORIES,
            'searchTerm' => $searchTerm
        ]);
    }
}
