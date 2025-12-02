<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Shared\Faker;

use App\Route\Domain\Entity\Station;
use App\Route\Domain\ValueObject\StationId;
use Faker\Factory;
use Faker\Generator;

class StationFaker
{
    private static Generator $faker;

    public static function createStation(): Station
    {
        $shortName = self::faker()->randomLetter();
        $longName = self::faker()->sentence(10);
        $id = StationId::fromInt(self::faker()->randomNumber());
        return new Station($id, $shortName, $longName);
    }

    public static function faker(): Generator
    {
        if (!isset(self::$faker)) {
            self::$faker = Factory::create();
        }
        return self::$faker;
    }
}
