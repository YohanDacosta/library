<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use Picqer\Barcode\Exceptions\BarcodeException;
use Picqer\Barcode\Exceptions\InvalidCharacterException;
use Picqer\Barcode\Renderers\PngRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Picqer\Barcode\Types\TypeCodabar;

class BookCrudController extends AbstractCrudController
{
    /**
     * @throws InvalidCharacterException
     * @throws BarcodeException
     */
    #[Route(path: '/barcode', name: 'download_barcode')]
    public function downloadBarcode($code): Response
    {
        $barcode = (new TypeCodabar())->getBarcode('22222');
        $renderer = new PngRenderer();

        $strBarcode = base64_encode($renderer->render($barcode, $barcode->getWidth() * 2));

        return new Response($strBarcode, 200, [
            'Content-Type' => 'image/jpeg; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$code.'-barcode.jpeg"',
            'Content-Length' => strlen($strBarcode),
        ]);
    }
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Book')
            ->setEntityLabelInPlural('Books')
            ->setSearchFields(['title', 'author']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $downloadBarcode = Action::new('downloadBarcode', 'Descargar Barcode')
            ->linkToRoute('download_barcode', function (Book $book) {
                return [
                    'code' => $book->getCode()
                ];
            })
            ->setCssClass('btn btn-success');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $downloadBarcode);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DatetimeFilter::new('created_at', 'Date'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addColumn(5),
            TextField::new('title'),
            TextField::new('author'),
            FormField::addColumn(4),
            TextField::new('isbn'),
            TextField::new('code', 'Barcode')
            ->onlyOnDetail()
            ->setTemplatePath('admin/field/barcode.html.twig'),
            FormField::addColumn(2),
            ChoiceField::new('status')->renderAsBadges([
                'available' => 'success',
                'loaned' => 'secondary',
                'reserved' => 'secondary',
                'repaired' => 'warning',
                'lost' => 'danger',
            ]),
            DatetimeField::new('createdAt', 'Date')->onlyOnIndex(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'Book added!');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'Book updated!');
    }
}
