<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Domain\Service;

use App\Security\Domain\ValueObject\UserIdentity;

interface AccessTokenGenerator
{
    public function generate(
        UserIdentity $userIdentity,
        int $expiresIn = 3600,
    ): string;
}
