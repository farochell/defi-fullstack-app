<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\Type;

use App\Security\Domain\ValueObject\UserId;
use App\Shared\Infrastructure\Doctrine\Type\UuidType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class UserIdType extends UuidType
{
    public const string TYPE = 'user_id';

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBinaryTypeDeclarationSQL([
            'length' => 16,
            'fixed' => true,
        ]);
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        $bin = hex2bin(str_replace('-', '', $value->toString()));

        if (false === $bin) {
            throw new \InvalidArgumentException(sprintf('Valeur invalide "%s" pour RouteIdType', $value->toString()));
        }

        return $bin;
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserId
    {
        if (null === $value) {
            return null;
        }

        $hex = bin2hex($value);
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($hex, 4));

        return UserId::fromString($uuid);
    }

    public function getName(): string
    {
        return self::TYPE;
    }
}
