<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\Course;
use App\Entity\Loan;
use App\Entity\School;
use App\Entity\Student;
use App\Entity\Tutor;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->redirect('/admin/book');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Kindergarten in Baselland')
            ->setlocales(['en', 'pl']);
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section('Library'),
            MenuItem::linkTo(BookCrudController::class,'Books', 'fas fa-book'),
            MenuItem::linkTo(BookCategoryCrudController::class, 'Category Books', 'fas fa-tags'),
            MenuItem::linkTo(LoanCrudController::class, 'Loans', 'fa fa-clock-o'),
            MenuItem::section('Academic'),
            MenuItem::linkTo(StudentCrudController::class, 'Students', 'fas fa-graduation-cap'),
            MenuItem::linkTo(CourseCrudController::class, 'Courses', 'fas fa-file'),
            MenuItem::linkTo(SchoolCrudController::class, 'Schools', 'fas fa-school'),
            MenuItem::linkTo(TutorCrudController::class, 'Tutors', 'fa fa-chalkboard-user'),
            MenuItem::section('Administration'),
            MenuItem::linkTo(UserCrudController::class, 'Users', 'fa fa-users')
        ];
    }
}
