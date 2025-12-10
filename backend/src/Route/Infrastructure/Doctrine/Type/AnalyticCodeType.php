<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\Infrastructure\Doctrine\Type;

use App\Route\Domain\ValueObject\AnalyticCodeEnum;
use App\Shared\Infrastructure\Doctrine\Type\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Webmozart\Assert\Assert;

class AnalyticCodeType extends StringType
{
    public const string NAME = 'analytic_code';

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?AnalyticCodeEnum
    {
        if (null === $value) {
            return null;
        }

        return AnalyticCodeEnum::tryFrom((string) $value);
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        Assert::isInstanceOf($value, AnalyticCodeEnum::class);

        $analyticCodeEnum = $value;

        return $analyticCodeEnum->value;
    }

    #[\Override]
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    #[\Override]
    public function getName(): string
    {
        return self::TYPE;
    }
}
