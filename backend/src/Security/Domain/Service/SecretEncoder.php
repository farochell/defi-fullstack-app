<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  automarkt
 */
declare(strict_types=1);

namespace App\Security\Domain\Service;

use App\Security\Domain\ValueObject\Password;

interface SecretEncoder
{
    public function encode(Password $password): Password;
}
