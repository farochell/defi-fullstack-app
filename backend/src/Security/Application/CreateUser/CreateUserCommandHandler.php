<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Application\CreateUser;

use App\Security\Domain\Service\UserCreator;
use App\Security\Domain\ValueObject\Email;
use App\Security\Domain\ValueObject\Password;
use App\Security\Domain\ValueObject\Role;
use App\Shared\Domain\Bus\Command\CommandHandler;

class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly UserCreator $userCreator,
    ) {
    }

    public function __invoke(CreateUserCommand $command): CreateUserResponse
    {
        $user = $this->userCreator->create(
            Email::fromString($command->email),
            Password::fromString($command->password),
            Role::USER
        );

        return new CreateUserResponse(
            $user->id->value(),
            $user->email->value()
        );
    }
}
