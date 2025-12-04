<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Integration\Security\Domain;

use App\Security\Domain\Entity\User;
use App\Security\Domain\Exception\EmailAlreadyExistException;
use App\Security\Domain\Repository\UserRepository;
use App\Security\Domain\Service\SecretEncoder;
use App\Security\Domain\Service\UserCreator;
use App\Security\Domain\ValueObject\Email;
use App\Security\Domain\ValueObject\Password;
use App\Security\Domain\ValueObject\Role;
use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;


class UserCreatorTest extends KernelTestCase
{
    private ?ContainerInterface $container;
    private ?UserRepository $userRepository;
    private ?SecretEncoder $secretEncoder;
    private ?UserCreator $userCreator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->userRepository = $this->container->get(UserRepository::class);
        $this->secretEncoder = $this->container->get(SecretEncoder::class);
        $this->userCreator = new UserCreator(
            $this->userRepository,
            $this->secretEncoder
        );
        $this->faker = \Faker\Factory::create();
    }

    public function testCreateUserSuccess(): void {
        $emailValue = $this->faker->email();
        $email = new Email($emailValue);
        $plain = new Password("Secret123!");
        $role = Role::USER;


        $user = $this->userCreator->create($email, $plain, $role);
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($emailValue, $user->email->value());
        $this->assertContains($role->value, $user->roles->toArray());
    }

    public function testThrowsExceptionWhenEmailAlreadyExists(): void
    {
        $email = new Email("demo@example.com");

        $this->expectException(EmailAlreadyExistException::class);

        $this->userCreator->create(
            $email,
            new Password("Secret123!"),
            Role::USER
        );
    }
}
