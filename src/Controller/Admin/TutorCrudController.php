<?php

namespace App\Controller\Admin;

use App\Entity\Tutor;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Validator\Constraints\NotNull;

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
            IdField::new('id')
                ->onlyOnIndex(),
            AssociationField::new('user')
                ->setFormTypeOption('constraints', [
                    new NotNull([
                        'message' => 'Debe seleccionar un usuario'
                    ])
                ])
                ->setFormTypeOption('query_builder', function ($er) {
                    return $er->createQueryBuilder('u')->leftJoin('u.tutor', 't')->where('t.id IS NULL');
                }),
            AssociationField::new('course'),
            DateTimeField::new('created_at')
                ->onlyOnIndex(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'Tutor added!');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'Tutor updated!');
    }
}
