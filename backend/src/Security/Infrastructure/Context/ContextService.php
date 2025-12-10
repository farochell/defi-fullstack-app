<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Infrastructure\Context;

interface ContextService
{
    public static function set(string $attribute, mixed $data): void;

    public function get(string $attribute): mixed;
}
