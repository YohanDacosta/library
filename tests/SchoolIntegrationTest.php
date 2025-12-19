<?php

namespace App\Tests;

use App\Entity\School;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SchoolIntegrationTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test']);
        $this->entityManager = $this->getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetAllSchool(): void
    {
        $schools = $this->entityManager->getRepository(School::class)->findAll();
        $this->assertIsArray($schools);
    }

    public function testCreateSchool(): void
    {
        $school = new School();
        $school->setName('School 1');
        $this->entityManager->persist($school);
        $this->entityManager->flush();
        $this->assertNotNull($school->getId());
        $this->assertEquals( 'School 1', $school->getName());
    }

    public function testUpdateCourse(): void
    {
        $school = $this->entityManager->getRepository(School::class)->find("45bc1263-ea2d-42d2-8881-ba0f35ef860e");
        $school->setName('School 2');
        $this->entityManager->flush();
        $this->assertEquals( 'School 2', $school->getName());
        $this->assertInstanceOf(School::class, $school);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
