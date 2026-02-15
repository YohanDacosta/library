<?php

namespace App\Tests\Integration;

use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\LoanIteam;
use App\Entity\Student;
use App\Entity\Tutor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoanIntegrationTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test', 'debug' => true]);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetLoans(): void
    {
        $loans = $this->entityManager->getRepository(Loan::class)->findAll();
        $this->assertIsArray($loans);
    }

    public function testCreateLoan(): void
    {

        $books = ['d33d5465-116d-424a-b027-c5839952dd3c', 'e23c6040-e57e-423d-8263-517e8826b14a'];
        $loan = new Loan();

//        $book = $this->entityManager->getRepository(Book::class)->find("192877fe-9be3-4670-9478-d0a6c31622cd");
//        $this->assertInstanceOf(Book::class, $book);

        $student = $this->entityManager->getRepository(Student::class)->find("89bb7fda-bf1b-40c1-bde8-3ff8dcddc5a1");
        $this->assertInstanceOf(Student::class, $student);

        $tutor = $this->entityManager->getRepository(Tutor::class)->find("11b78ba9-a5c0-4d06-8b2c-7c8a30cc7673");
        $this->assertInstanceOf(Tutor::class, $tutor);

        foreach ($books as $bookId) {
            $book = $this->entityManager->getRepository(Book::class)->find($bookId);
            $loanItemBook = new LoanIteam();
            $loanItemBook->setBook($book);
            $loan->addLoanIteam($loanItemBook);
        }

        $loan->setStudent($student);
        $loan->setTutor($tutor);
        $loan->setReturnDate(new \DateTimeImmutable);
       $this->entityManager->persist($loan);
       $this->entityManager->flush();

       $this->assertNotNull($loan->getId());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
