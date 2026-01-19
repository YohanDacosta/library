<?php

namespace App\Tests\Integration;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserIntegrationTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test', 'debug' => true]);
        $this->entityManager = $this->getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetUsers(): void
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $this->assertIsArray($users);
    }

    public function testCreateUser(): void
    {
        $user = new User();
        $user->setEmail('yohan@gmail.com');
        $user->setPassword('admin123');
        $user->setFirstName('Yohan');
        $user->setLastName('Diaz Acosta');

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->assertInstanceOf(EntityManagerInterface::class, $user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
