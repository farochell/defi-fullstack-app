<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Application\CreateUser;

use App\Shared\Domain\Bus\Command\Command;

class CreateUserCommand implements Command
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}
