<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Domain\Event;

use App\Route\Domain\Entity\Route;
use App\Route\Domain\Entity\Station;
use App\Shared\Domain\Bus\Event\DomainEvent;

final class RouteCreatedDomainEvent extends DomainEvent
{
    /**
     * @param string $id
     * @param array<string, mixed> $fromStation
     * @param array<string, mixed> $toStation
     * @param string $analyticCode
     * @param float $distanceKm
     * @param array<Station> $path
     * @param string $createdAt
     */
    public function __construct(
        public readonly string $id,
        public readonly array $fromStation,
        public readonly array $toStation,
        public readonly string $analyticCode,
        public readonly float $distanceKm,
        public readonly array $path,
        public readonly string $createdAt
    )
    {
    }

    public static function eventName(): string
    {
        return 'route.created';
    }

}
