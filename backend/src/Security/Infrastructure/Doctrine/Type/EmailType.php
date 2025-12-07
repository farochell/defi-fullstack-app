<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\Type;

use App\Security\Domain\ValueObject\Email;
use App\Shared\Infrastructure\Doctrine\Type\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Override;

class EmailType extends StringType
{
    public const string TYPE = 'email';

    #[Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): Email
    {
        return Email::fromString((string) $value);
    }
}
