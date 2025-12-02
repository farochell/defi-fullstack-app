<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Application\Login;

use App\Security\Domain\Exception\InvalidCredentialsException;
use App\Security\Domain\Repository\UserRepository;
use App\Security\Domain\Service\PasswordHasher;
use App\Security\Domain\ValueObject\Email;
use App\Security\Domain\ValueObject\Password;
use App\Security\Domain\ValueObject\Role;
use App\Shared\Domain\Bus\Query\QueryHandler;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

use function Lambdish\Phunctional\map;


class LoginQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHasher $passwordHasher,
    ) {}

    public function __invoke(LoginQuery $query): LoginResponse {
        $user = $this->userRepository->findByEmail(
            Email::fromString($query->email),
        );
        if (!$user) {
            throw new UserNotFoundException($query->email);
        }

        if (!$this->passwordHasher->verify(
            $user->hashedPassword,
            Password::fromString($query->password)
        )) {
            throw new InvalidCredentialsException();
        }

        $roles = map(
            fn(Role $role): string => $role->value,
            $user->roles
        );
        return new LoginResponse(
            $user->id->value(),
            $user->email->value(),
            $roles,
        );
    }
}
