<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Domain\EventSubscriber;

use App\Route\Domain\Entity\Route;
use App\Route\Domain\Entity\Station;
use App\Route\Domain\Event\RouteCreatedDomainEvent;
use App\Route\Domain\Repository\RouteRepositoryInterface;
use App\Route\Domain\ValueObject\AnalyticCodeEnum;
use App\Route\Domain\ValueObject\RouteId;
use App\Route\Domain\ValueObject\Stations;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;
use DateTimeImmutable;
use function Lambdish\Phunctional\map;

class RouteCreatedEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private readonly RouteRepositoryInterface $routeRepo,
    ) {}
    public function __invoke(RouteCreatedDomainEvent $event): void
    {
        $routeId = RouteId::fromString($event->id);
        $fromStation = Station::create(
            $event->fromStation['id'],
            $event->fromStation['shortName'],
            $event->fromStation['longName']
        );
        $toStation = Station::create(
            $event->toStation['id'],
            $event->toStation['shortName'],
            $event->toStation['longName']
        );
        $analyticCode = AnalyticCodeEnum::from($event->analyticCode);
        $distanceKm = $event->distanceKm;
        $path = map(
            fn (array $station) => Station::create(
                $station['id'],
                $station['shortName'],
                $station['longName']
            ),
            $event->path
        );
        $createdAt = $event->createdAt;
        $this->routeRepo->save(
            new Route(
                $routeId,
                $fromStation,
                $toStation,
                $analyticCode,
                $distanceKm,
                new Stations($path),
                new DateTimeImmutable($createdAt)
            )
        );
    }

    /**
     * @return class-string[]
     */
    public static function subscribedTo(): array {
        return [RouteCreatedDomainEvent::class];
    }
}
