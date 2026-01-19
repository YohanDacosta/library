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
        $book->setTitle("Django2");
        $book->setAuthor("Antonio Mele");
        $book->setCode("9788425727480");
        $book->setCopiesAvailable(20);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $this->assertNotNull($book->getId());
    }

    public function testGetBook()
    {
        $book = $this->entityManager->getRepository(Book::class)->find("192877fe-9be3-4670-9478-d0a6c31622cd");
        $this->assertInstanceOf(Book::class, $book);
    }

    public function testUpdateBook()
    {
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(["id" => "192877fe-9be3-4670-9478-d0a6c31622cd"]);

        $this->assertInstanceOf(Book::class, $book);
        $book->setTitle("Django3");
        $this->entityManager->flush();
        $this->assertEquals("Django3", $book->getTitle());
    }

    public function testDeleteBook()
    {
        $book = $this->entityManager->getRepository(Book::class)->find("2356f290-1cab-4e13-9034-257036fbecdb");

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
