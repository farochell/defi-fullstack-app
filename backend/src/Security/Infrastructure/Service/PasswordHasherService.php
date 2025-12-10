<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  api-monagil
 */

declare(strict_types=1);

namespace App\Security\Infrastructure\Service;

use App\Security\Domain\Service\PasswordHasher;
use App\Security\Domain\ValueObject\HashedPassword;
use App\Security\Domain\ValueObject\Password;
use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class PasswordHasherService implements PasswordHasher
{
    use CheckPasswordLengthTrait;

    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
    }

    public function hash(Password $password): HashedPassword
    {
        if ($this->isPasswordTooLong((string) $password)) {
            throw new InvalidPasswordException();
        }
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(SymfonyPasswordHasherBridge::class);

        return HashedPassword::fromString($passwordHasher->hash((string) $password));
    }

    public function verify(HashedPassword $hashedPassword, Password $plainPassword): bool
    {
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(SymfonyPasswordHasherBridge::class);

        return $passwordHasher->verify((string) $hashedPassword, (string) $plainPassword);
    }
}
