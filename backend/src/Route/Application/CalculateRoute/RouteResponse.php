<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Application\CalculateRoute;

use App\Shared\Application\SerializableResponse;
use App\Shared\Domain\Bus\Command\CommandResponse;

class RouteResponse extends SerializableResponse implements CommandResponse {
    public function __construct(
        public string $id,
        public string $fromStationId,
        public string $toStationId,
        public string $analyticCode,
        public float $distanceKm,
        public array $path,
        public string $createdAt
    ) {}
    public function jsonSerialize(): mixed {
        return [
            'id' => $this->id,
            'fromStationId' => $this->fromStationId,
            'toStationId' => $this->toStationId,
            'analyticCode' => $this->analyticCode,
            'distanceKm' => $this->distanceKm,
            'path' => $this->path,
            'createdAt' => $this->createdAt
        ];
    }
}
