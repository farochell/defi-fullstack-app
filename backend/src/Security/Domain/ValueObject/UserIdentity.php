<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Domain\ValueObject;

class UserIdentity
{
    /**
     * @param array<string> $roles
     */
    public function __construct(
        public string $userId,
        public string $username,
        public array $roles,
    ) {
    }
}
