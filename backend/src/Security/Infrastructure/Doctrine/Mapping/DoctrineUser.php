<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\Mapping;

use App\Security\Domain\ValueObject\Email;
use App\Security\Domain\ValueObject\HashedPassword;
use App\Security\Domain\ValueObject\Roles;
use App\Security\Domain\ValueObject\UserId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class DoctrineUser
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'user_id', unique: true)]
    public UserId $id;

    #[ORM\Column(type: 'email', length: 100, unique: true)]
    public Email $email;

    #[ORM\Column(type: 'roles')]
    public Roles $roles;

    #[ORM\Column(type: 'hashed_password', length: 255)]
    public HashedPassword $hashedPassword;
}
