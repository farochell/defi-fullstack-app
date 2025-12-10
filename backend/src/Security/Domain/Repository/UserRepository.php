<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Domain\Repository;

use App\Security\Domain\Entity\User;
use App\Security\Domain\ValueObject\Email;

interface UserRepository
{
    public function save(User $user): void;

    public function findByEmail(Email $email): ?User;
}
