<?php

namespace App\Tests\Integration;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BookIntegrationTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager =  null;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test']);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }
    public function testGetBooks()
    {
        $this->assertInstanceOf(EntityManagerInterface::class, $this->entityManager);
        $books = $this->entityManager->getRepository(Book::class)->findAll();
        $this->assertIsArray($books);

        foreach ($books as $book) {
            $this->assertInstanceOf(Book::class, $book);
        }
    }

    public function testCreateBook()
    {
        $book = new Book();
        $book->setTitle("Python");
        $book->setAuthor("Brian Schan");
        $book->setCode("9788425727482");

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $this->assertNotNull($book->getId());
    }

    public function testGetBook()
    {
        $book = $this->entityManager->getRepository(Book::class)->find("e23c6040-e57e-423d-8263-517e8826b14a");
        $this->assertInstanceOf(Book::class, $book);
    }

    public function testUpdateBook()
    {
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(["id" => "e23c6040-e57e-423d-8263-517e8826b14a"]);

        $this->assertInstanceOf(Book::class, $book);
        $book->setTitle("Django3");
        $this->entityManager->flush();
        $this->assertEquals("Django3", $book->getTitle());
    }

    public function testDeleteBook()
    {
        $book = $this->entityManager->getRepository(Book::class)->find("d4e5b107-03d8-4ba9-8422-f8dd112063c6");

        if ($book instanceof Book) {
            $this->entityManager->remove($book);
            $this->entityManager->flush();
            $this->assertNull($book);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;

    }
}
