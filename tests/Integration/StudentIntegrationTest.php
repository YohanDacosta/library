<?php

namespace App\Tests\Integration;

use App\Entity\Course;
use App\Entity\School;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StudentIntegrationTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test', 'debug' => true]);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetAllStudent(): void
    {
        $students = $this->entityManager->getRepository(Student::class)->findAll();
        $this->assertIsArray($students);
    }

    public function testGetStudent(): void
    {
        $student = $this->entityManager->getRepository(Student::class)->find("89bb7fda-bf1b-40c1-bde8-3ff8dcddc5a1");
        $this->assertInstanceOf(Student::class, $student);
    }

    public function testCreateStudent(): void
    {
        $course = $this->entityManager->getRepository(Course::class)->find("c6671136-9a40-4934-ad60-20b930b19cce");
        $this->assertInstanceOf(Course::class, $course);

        $school = $this->entityManager->getRepository(School::class)->find("45bc1263-ea2d-42d2-8881-ba0f35ef860e");
        $this->assertInstanceOf(School::class, $school);

        $student = new Student();
        $student->setFirstName('Yohan');
        $student->setLastName('Diaz Acosta');
        $student->setEmail('yohan@gmail.com');
        $student->setCourse($course);
        $student->setSchool($school);

        $this->entityManager->persist($student);
        $this->entityManager->flush();
        $this->assertEquals('Yohan', $student->getFirstName());
    }

    public function testUpdateStudent(): void
    {
        $student = $this->entityManager->getRepository(Student::class)->find("89bb7fda-bf1b-40c1-bde8-3ff8dcddc5a1");
        $student->setFirstName("Yausmara");
        $this->entityManager->flush();

        $this->assertInstanceOf(Student::class, $student);
    }

    public function testUpdateCourseStudent($id = "89bb7fda-bf1b-40c1-bde8-3ff8dcddc5a1", $idCourse = "248e181b-e991-4a9e-9873-6e164c5a12c1"): void
    {
        $student = $this->entityManager->getRepository(Student::class)->find($id);
        $this->assertInstanceOf(Student::class, $student);

        $course = $this->entityManager->getRepository(Course::class)->find($idCourse);
        $this->assertInstanceOf(Course::class, $course);

        $student->setCourse($course);
        $this->entityManager->flush();
        $this->assertEquals($course, $student->getCourse());
    }

    public function testUpdateSchoolStudent($id = "89bb7fda-bf1b-40c1-bde8-3ff8dcddc5a1", $idSchool = "1fcfe71a-2a04-4b0e-9dbb-df6a82051888"): void
    {
        $student = $this->entityManager->getRepository(Student::class)->find($id);
        $this->assertInstanceOf(Student::class, $student);

        $school = $this->entityManager->getRepository(School::class)->find($idSchool);
        $this->assertInstanceOf(School::class, $school);

        $student->setSchool($school);
        $this->entityManager->flush();
        $this->assertEquals($school, $student->getSchool());
    }

    public function testFindStudentsByTutor($idTutor = "11b78ba9-a5c0-4d06-8b2c-7c8a30cc7673"): void
    {
        $students = $this->entityManager->getRepository(Student::class)
            ->createQueryBuilder('s')
            ->join('s.course', 'c')
            ->join('c.tutors', 't')
            ->where('t.id = :id')
            ->setParameter('id', $idTutor, 'uuid')
            ->getQuery()
            ->getResult();

        $this->assertIsArray($students);
        $this->assertCount(1, $students);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
