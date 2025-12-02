<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Unit\Security\Domain;

use App\Security\Domain\Entity\User;
use App\Security\Domain\Exception\EmailAlreadyExistException;
use App\Security\Domain\Repository\UserRepository;
use App\Security\Domain\Service\SecretEncoder;
use App\Security\Domain\Service\UserCreator;
use App\Security\Domain\ValueObject\Email;
use App\Security\Domain\ValueObject\HashedPassword;
use App\Security\Domain\ValueObject\Password;
use App\Security\Domain\ValueObject\Role;
use PHPUnit\Framework\TestCase;

class UserCreatorTest extends TestCase
{
    private $repository;
    private $encoder;
    private UserCreator $creator;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->encoder = $this->createMock(SecretEncoder::class);

        $this->creator = new UserCreator(
            $this->repository,
            $this->encoder
        );
    }

    public function testCreatesUserSuccessfully(): void {
        $email = new Email("demo@example.com");
        $plain = new Password("Secret123!");
        $role = Role::USER;
        $bcrypt = password_hash("Secret123!", PASSWORD_BCRYPT);
        // repo ne trouve personne → email disponible
        $this->repository->method('findByEmail')->with($email)->willReturn(null);

        $this->encoder
            ->method('encode')
            ->with($plain)
            ->willReturn(Password::fromString($bcrypt));

        // repo doit appeler save() exactement une fois
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $user = $this->creator->create($email, $plain, $role);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame("demo@example.com", $user->email->value());
        $this->assertSame($bcrypt, $user->hashedPassword->value());
        $this->assertContains($role->value, $user->roles->toArray());
    }

    public function testThrowsExceptionWhenEmailAlreadyExists(): void
    {
        $email = new Email("demo@example.com");

        // repo trouve déjà un user
        $this->repository
            ->method('findByEmail')
            ->with($email)
            ->willReturn($this->createMock(User::class));

        $this->expectException(EmailAlreadyExistException::class);

        $this->creator->create(
            $email,
            new Password("Secret123!"),
            Role::USER
        );
    }

    public function testPasswordIsEncoded(): void
    {
        $email = new Email("demo@example.com");
        $plain = new Password("Secret123!");

        $this->repository
            ->method('findByEmail')
            ->willReturn(null);

        $bcrypt = password_hash("Secret123!", PASSWORD_BCRYPT);
        $this->encoder
            ->expects($this->once())
            ->method('encode')
            ->with($plain)
            ->willReturn(Password::fromString($bcrypt));

        $user = $this->creator->create($email, $plain, Role::USER);
        $this->assertSame($bcrypt, $user->hashedPassword->value());
    }

    public function testRolesAreCorrectlyApplied(): void
    {
        $email = new Email("demo@example.com");

        $this->repository->method('findByEmail')->willReturn(null);
        $bcrypt = password_hash("Secret123!", PASSWORD_BCRYPT);
        $this->encoder->method('encode')->willReturn(Password::fromString($bcrypt));
        $user = $this->creator->create(
            $email,
            new Password("Secret123!"),
            Role::USER
        );

        $this->assertSame(['ROLE_USER'], $user->roles->toArray());
    }
}
