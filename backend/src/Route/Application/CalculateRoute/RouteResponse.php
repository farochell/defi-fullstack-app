<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Application\CalculateRoute;

use App\Route\Domain\Entity\Station;
use App\Route\Domain\ValueObject\Stations;
use App\Shared\Application\SerializableResponse;
use App\Shared\Domain\Bus\Command\CommandResponse;

use function Lambdish\Phunctional\map;

class RouteResponse extends SerializableResponse implements CommandResponse
{
    public function __construct(
        public string $id,
        public string $fromStationId,
        public string $toStationId,
        public string $analyticCode,
        public float $distanceKm,
        public Stations $path,
        public string $createdAt,
    ) {
    }

    public static function fromDomain(
        string $id,
        string $fromStationId,
        string $toStationId,
        string $analyticCode,
        float $distanceKm,
        Stations $path,
        string $createdAt,
    ): self {
        return new self(
            $id,
            $fromStationId,
            $toStationId,
            $analyticCode,
            $distanceKm,
            $path,
            $createdAt
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'fromStationId' => $this->fromStationId,
            'toStationId' => $this->toStationId,
            'analyticCode' => $this->analyticCode,
            'distanceKm' => $this->distanceKm,
            'path' => map(
                fn (Station $station) => StationResponse::fromDomain($station),
                $this->path
            ),
            'createdAt' => $this->createdAt,
        ];
    }
}
