<?php

namespace App\Tests;

use App\Entity\BookCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BookCategoryIntegrationTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test']);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetAllBookCategories()
    {
        $bookCategories = $this->entityManager->getRepository(BookCategory::class)->findAll();
        $this->assertIsArray($bookCategories);
    }

    public function testCreateBookCategory(): void
    {
        $bookCategory = new BookCategory();
        $bookCategory->setCategory("Programming");
        $bookCategory->setDescription("This book is a programming language");
        $this->entityManager->persist($bookCategory);
        $this->entityManager->flush();

        $this->assertNotNull($bookCategory->getId());
        $this->assertEquals("Programming", $bookCategory->getCategory());
    }

    public function testGetBookCategories()
    {
        $bookCategories = $this->entityManager->getRepository(BookCategory::class)->find("f0d7a7a5-3f14-44da-a9a3-5d6d95b33329");
        $this->assertInstanceOf(BookCategory::class, $bookCategories);
    }

    public function testUpdateBookCategory(): void
    {
        $bookCategory = $this->entityManager->getRepository(BookCategory::class)->find("6143d260-983f-4045-adec-12df04b66da8");
        $this->assertInstanceOf(BookCategory::class, $bookCategory);
        $bookCategory->setCategory("History");
        $bookCategory->setDescription("This book is a history of Switzerland");
        $this->entityManager->persist($bookCategory);
        $this->entityManager->flush();

        $this->assertEquals("History", $bookCategory->getCategory());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
