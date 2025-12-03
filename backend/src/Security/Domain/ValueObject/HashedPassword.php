<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidFormat;
use App\Shared\Domain\ValueObject\StringValueObject;

class HashedPassword extends StringValueObject
{
    private const string ARGON2ID_PATTERN = '/^\$argon2id\$v=\d+\$m=\d+,t=\d+,p=\d+\$[A-Za-z0-9\/+]+={0,2}\$[A-Za-z0-9\/+]+={0,2}$/';

    /**
     * @throws InvalidFormat
     */
    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->ensureIsArgon2idPattern();
    }

    private function ensureIsArgon2idPattern(): void
    {
        $hashedPassword = $this->value;

        if (!preg_match(self::ARGON2ID_PATTERN, $hashedPassword)) {
            throw new InvalidFormat('The hashed password argon2id encoded format');
        }
    }

}
