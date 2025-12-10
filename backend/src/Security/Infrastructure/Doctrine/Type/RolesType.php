<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\Type;

use App\Security\Domain\ValueObject\Roles;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class RolesType extends JsonType
{
    public const string NAME = 'roles';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Roles) {
            $value = $value->toArray();
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Roles
    {
        $array = parent::convertToPHPValue($value, $platform);

        if (null === $array) {
            return null;
        }

        return Roles::fromArray($array);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
