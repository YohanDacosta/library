<?php

namespace App\Controller\Admin;

use App\Entity\Tutor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class TutorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tutor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addColumn(4),
            IdField::new('id')->onlyOnIndex(),
            TextField::new('first_name'),
            TextField::new('last_name'),
            TextField::new('email'),
            FormField::addColumn(2),
            DateTimeField::new('created_at')->onlyOnIndex(),
            DateTimeField::new('updated_at')->onlyOnForms()->hideWhenCreating(),
            AssociationField::new('courses'),
            AssociationField::new('schools'),
        ];
    }
}
