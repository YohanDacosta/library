<?php

namespace App\Tests;

use App\Entity\Course;
use App\Entity\School;
use App\Entity\Tutor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TutorIntegrationTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test', 'debug' => true]);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetTutors(): void
    {
        $tutors = $this->entityManager->getRepository(Tutor::class)->findAll();
        $this->assertIsArray($tutors);
    }

    public function testCreateTutorWithoutSchoolAndCourse(): void
    {
        $tutor = new Tutor();
        $tutor->setFirstName('Yausmara');
        $tutor->setLastName('Diaz');
        $tutor->setEmail('yausmara@gmail.com');

        $this->entityManager->persist($tutor);
        $this->entityManager->flush();

        $this->assertNotNull($tutor->getId());
        $this->assertEquals('Yausmara', $tutor->getFirstName());
    }

    public function testCreateTutorWithSchoolAndCourse($idCourse = "f4c79ec5-ea4e-4658-91b6-2cd39687874e", $idSchool = "45bc1263-ea2d-42d2-8881-ba0f35ef860e"): void
    {
        $tutor = new Tutor();
        $tutor->setFirstName('Aismara');
        $tutor->setLastName('Diaz');
        $tutor->setEmail('aismara@gmail.com');

        $course = $this->entityManager->getRepository(Course::class)->find($idCourse);
        $this->assertInstanceOf(Course::class, $course);
        $tutor->addCourse($course);

        $school = $this->entityManager->getRepository(School::class)->find($idSchool);
        $this->assertInstanceOf(School::class, $school);
        $tutor->addSchool($school);

        $this->entityManager->persist($tutor);
        $this->entityManager->flush();

        $this->assertNotNull($tutor->getId());
        $this->assertEquals('Aismara', $tutor->getFirstName());
    }

    public function testUpdateTutor($id = "11b78ba9-a5c0-4d06-8b2c-7c8a30cc7673"): void
    {
        $tutor = $this->entityManager->getRepository(Tutor::class)->find($id);
        $this->assertInstanceOf(Tutor::class, $tutor);

        $tutor->setEmail("aismara3@gmail.com");
        $tutor->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->assertEquals('aismara3@gmail.com', $tutor->getEmail());
    }

    public function testUpdateCourseTutor($id = "11b78ba9-a5c0-4d06-8b2c-7c8a30cc7673", $idCourse = "961080dd-e8f0-49dc-8b6d-18e9fd50cdb4"): void
    {
        $tutor = $this->entityManager->getRepository(Tutor::class)->find($id);
        $this->assertInstanceOf(Tutor::class, $tutor);

        $course = $this->entityManager->getRepository(Course::class)->find($idCourse);
        $tutor->addCourse($course);
        $this->entityManager->flush();

        $this->assertEquals( $idCourse, $course->getId());
    }

    public function testRemoveCourseTutor($id = "11b78ba9-a5c0-4d06-8b2c-7c8a30cc7673", $idCourse = "961080dd-e8f0-49dc-8b6d-18e9fd50cdb4"): void
    {
        $tutor = $this->entityManager->getRepository(Tutor::class)->find($id);
        $this->assertInstanceOf(Tutor::class, $tutor);

        $course = $this->entityManager->getRepository(Course::class)->find($idCourse);
        $tutor->removeCourse($course);
        $this->entityManager->flush();

        $this->assertEquals( $idCourse, $course->getId());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
