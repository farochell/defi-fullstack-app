<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Domain\Service;

use App\Security\Domain\ValueObject\AccessToken;

interface AccessTokenDecoder
{
    public function decode(
        string $token,
    ): AccessToken;
}
