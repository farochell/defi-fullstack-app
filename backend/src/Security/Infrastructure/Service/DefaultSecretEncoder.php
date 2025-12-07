<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  api-monagil
 */
declare(strict_types=1);

namespace App\Security\Infrastructure\Service;

use App\Security\Domain\Service\SecretEncoder;
use App\Security\Domain\ValueObject\Password;

class DefaultSecretEncoder implements SecretEncoder
{
    public function encode(Password $password): Password
    {
        $hashedPassword = password_hash(
            $password->value(),
            PASSWORD_ARGON2ID,
        );

        return Password::fromString($hashedPassword);
    }
}
