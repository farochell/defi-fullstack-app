<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Domain\ValueObject;

class AccessToken
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        public string $content,
        public string $id,
        public string $username,
        public array $roles,
        public int $createdAt,
        public int $expiresAt,
    ) {}
}
