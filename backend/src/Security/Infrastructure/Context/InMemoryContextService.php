<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Infrastructure\Context;

class InMemoryContextService implements ContextService
{
    /**
     * @var array<mixed>
     */
    private static array $context = [];

    public static function set(string $attribute, mixed $data): void
    {
        self::$context[$attribute] = $data;
    }

    public function get(string $attribute): mixed
    {
        return self::$context[$attribute] ?? null;
    }
}
