<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Application\Login;

use App\Shared\Domain\Bus\Query\Query;

class LoginQuery implements Query
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
    }
}
