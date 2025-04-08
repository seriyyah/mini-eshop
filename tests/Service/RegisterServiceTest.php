<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\RegisterService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private RegisterService $registerService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->entityManager->expects($this->any())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));
        $this->entityManager->expects($this->any())
            ->method('flush');

        $this->passwordHasher->expects($this->any())
            ->method('hashPassword')
            ->willReturn('hashedpassword');

        $this->registerService = new RegisterService($this->entityManager, $this->passwordHasher);
    }

    public function testRegisterUserSuccessfully(): void
    {
        $user = $this->registerService->registerUser('newuser@example.com', 'secret123');
        $this->assertSame('newuser@example.com', $user->getEmail());
        $this->assertSame('hashedpassword', $user->getPassword());
    }
}