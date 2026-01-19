<?php

namespace App\Tests\Integration;

use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CourseIntegrationTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test']);
        $this->entityManager = $this->getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetAllCourse(): void
    {
        $courses = $this->entityManager->getRepository(Course::class)->findAll();
        $this->assertIsArray($courses);
    }

    public function testCreateCourse(): void
    {
        $course = new Course();
        $course->setName('Group 2');
        $this->entityManager->persist($course);
        $this->entityManager->flush();
        $this->assertNotNull($course->getId());
        $this->assertEquals( 'Group 2', $course->getName());
    }

    public function testUpdateCourse(): void
    {
        $course = $this->entityManager->getRepository(Course::class)->find("248e181b-e991-4a9e-9873-6e164c5a12c1");
        $course->setName('Group 3');
        $this->entityManager->flush();
        $this->assertEquals( 'Group 3', $course->getName());
        $this->assertInstanceOf(Course::class, $course);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
