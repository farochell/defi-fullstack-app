<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Domain\Service;

use App\Security\Domain\ValueObject\HashedPassword;
use App\Security\Domain\ValueObject\Password;

interface PasswordHasher
{
    public function hash(Password $password): HashedPassword;

    public function verify(HashedPassword $hashedPassword, Password $plainPassword): bool;
}
