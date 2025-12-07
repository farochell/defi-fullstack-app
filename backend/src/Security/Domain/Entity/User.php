<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Domain\Entity;

use App\Security\Domain\ValueObject\Email;
use App\Security\Domain\ValueObject\HashedPassword;
use App\Security\Domain\ValueObject\Roles;
use App\Security\Domain\ValueObject\UserId;
use App\Shared\Domain\Aggregate\AggregateRoot;

class User extends AggregateRoot
{
    public function __construct(
        public readonly UserId $id,
        public readonly Email $email,
        public readonly HashedPassword $hashedPassword,
        public readonly Roles $roles,
    ) {
    }

    public static function create(
        Email $email,
        HashedPassword $hashedPassword,
        Roles $roles,
    ): User {
        return new self(
            id: UserId::random(),
            email: $email,
            hashedPassword: $hashedPassword,
            roles: $roles
        );
    }
}
