<?php

namespace App\DataFixtures;

use App\Security\Domain\Service\SecretEncoder;
use App\Security\Domain\ValueObject\Email;
use App\Security\Domain\ValueObject\HashedPassword;
use App\Security\Domain\ValueObject\Password;
use App\Security\Domain\ValueObject\Role;
use App\Security\Domain\ValueObject\Roles;
use App\Security\Domain\ValueObject\UserId;
use App\Security\Infrastructure\Doctrine\Mapping\DoctrineUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly SecretEncoder $encoder,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new DoctrineUser();
        $user->id = UserId::random();
        $user->email = Email::fromString('demo@example.com');
        $user->roles = new Roles([Role::fromString('ROLE_USER')]);
        $hashedPassword = HashedPassword::fromString(
            $this->encoder->encode(Password::fromString('password'))->value()
        );
        $user->hashedPassword = $hashedPassword;
        $manager->persist($user);

        $manager->flush();
    }
}
