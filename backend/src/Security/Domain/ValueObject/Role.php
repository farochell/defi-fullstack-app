<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Domain\ValueObject;

enum Role: string
{
    // Utilisation d'un enum pour prévoir d'autres rôles a l'avenir
    case USER = 'ROLE_USER';

    public static function fromString(string $value): self
    {
        if (!($role = self::tryFrom($value))) {
            throw new \InvalidArgumentException("Invalid value for Role: $value");
        }

        return $role;
    }
}
