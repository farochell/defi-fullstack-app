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
    private const string BCRYPT_PATTERN = '/^\$2[ayb]\$.{56}$/';

    /**
     * @throws InvalidFormat
     */
    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->ensureIsBcryptPattern();
    }

    private function ensureIsBcryptPattern(): void
    {
        $hashedPassword = $this->value;

        if (!preg_match(self::BCRYPT_PATTERN, $hashedPassword)) {
            throw new InvalidFormat('The hashed password isn\'t bcrypt encoded format');
        }
    }

}
