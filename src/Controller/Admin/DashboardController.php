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
            MenuItem::linkToCrud('Books', 'fas fa-book', Book::class),
            MenuItem::linkToCrud('Category Books', 'fas fa-tags', BookCategory::class),
            MenuItem::linkToCrud('Loans', 'fa fa-clock-o', Loan::class),

            MenuItem::section('Academic'),
            MenuItem::linkToCrud('Students', 'fas fa-graduation-cap', Student::class),
            MenuItem::linkToCrud('Courses', 'fas fa-file', Course::class),
            MenuItem::linkToCrud('Schools', 'fas fa-school', School::class),
            MenuItem::linkToCrud('Tutors', 'fa fa-chalkboard-user', Tutor::class),

            MenuItem::section('Administration'),
            MenuItem::linkToCrud('Users', 'fa fa-users', User::class),
        ];
    }
}
