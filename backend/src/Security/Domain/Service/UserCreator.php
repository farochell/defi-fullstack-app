<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Domain\Service;

use App\Security\Domain\Entity\User;
use App\Security\Domain\Exception\EmailAlreadyExistException;
use App\Security\Domain\Repository\UserRepository;
use App\Security\Domain\ValueObject\Email;
use App\Security\Domain\ValueObject\HashedPassword;
use App\Security\Domain\ValueObject\Password;
use App\Security\Domain\ValueObject\Role;
use App\Security\Domain\ValueObject\Roles;

class UserCreator
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly SecretEncoder $encoder,
    ) {
    }

    public function create(
        Email $email,
        Password $plainPassword,
        Role $role,
    ): User {
        if ($this->repository->findByEmail($email)) {
            throw new EmailAlreadyExistException();
        }
        $hashedPassword = HashedPassword::fromString(
            $this->encoder->encode($plainPassword)->value()
        );

        $user = User::create(
            $email,
            $hashedPassword,
            Roles::fromArray([$role->value]),
        );

        $this->repository->save($user);

        return $user;
    }
}
