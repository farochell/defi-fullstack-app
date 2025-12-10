<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\Domain\Entity;

use App\Route\Domain\Event\RouteCreatedDomainEvent;
use App\Route\Domain\ValueObject\AnalyticCodeEnum;
use App\Route\Domain\ValueObject\RouteId;
use App\Route\Domain\ValueObject\Stations;
use App\Shared\Domain\Aggregate\AggregateRoot;

use function Lambdish\Phunctional\map;

class Route extends AggregateRoot
{
    public function __construct(
        public readonly RouteId $id,
        public readonly Station $fromStation,
        public readonly Station $toStation,
        public readonly AnalyticCodeEnum $analyticCode,
        public readonly float $distanceKm,
        public readonly Stations $path,
        public readonly \DateTimeImmutable $createdAt,
    ) {
    }

    public static function create(
        Station $fromStation,
        Station $toStation,
        AnalyticCodeEnum $analyticCode,
        float $distanceKm,
        Stations $path,
    ): self {
        $route = new self(
            RouteId::random(),
            $fromStation,
            $toStation,
            $analyticCode,
            $distanceKm,
            $path,
            new \DateTimeImmutable()
        );
        $route->recordThat(
            new RouteCreatedDomainEvent(
                $route->id->value(),
                [
                    'id' => $route->fromStation->id->value(),
                    'shortName' => $route->fromStation->shortName,
                    'longName' => $route->fromStation->longName,
                ],
                [
                    'id' => $route->toStation->id->value(),
                    'shortName' => $route->toStation->shortName,
                    'longName' => $route->toStation->longName,
                ],
                $route->analyticCode->value,
                $route->distanceKm,
                map(
                    fn (Station $station) => [
                        'id' => $station->id->value(),
                        'shortName' => $station->shortName,
                        'longName' => $station->longName,
                    ],
                    $route->path
                ),
                $route->createdAt->format('Y-m-d H:i:s')
            )
        );

        return $route;
    }
}
