<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use Symfony\Component\Validator\Constraints\NotNull;


class StudentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Student::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Student')
            ->setEntityLabelInPlural('Students')
            ->setSearchFields(['first_name', 'last_name']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addColumn(5),
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('first_name'),
            TextField::new('last_name'),
            FormField::addColumn(3),
            DateTimeField::new('created_at')
                ->onlyOnIndex(),
            DateTimeField::new('updated_at')
                ->onlyOnForms()
                ->onlyWhenUpdating(),
            AssociationField::new('course')
                ->setRequired(true)
                ->setFormTypeOption('constraints', [
                    new NotNull([
                        'message' => 'Debe seleccionar un curso'
                    ])
                ]),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'Student added!');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'Student updated!');
    }
}
