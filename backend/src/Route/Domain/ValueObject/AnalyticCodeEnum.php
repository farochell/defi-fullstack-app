<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Domain\ValueObject;

enum AnalyticCodeEnum: string
{
    case FRET = 'FRET';
    case PASSENGER = 'PASSAGER';
    case MAINTENANCE = 'MAINTENANCE';

    public static function tryFromName(string $name): ?self
    {
        foreach(self::cases() as $case) {
            if (strtoupper($name) === $case->name) {
                return self::from($case->value);
            }
        }
        return null;
    }
}
