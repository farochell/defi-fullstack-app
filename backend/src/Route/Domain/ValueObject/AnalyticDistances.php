<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Domain\ValueObject;

use App\Shared\Domain\Collection;
use function Lambdish\Phunctional\map;

class AnalyticDistances extends Collection
{
    protected function type(): string {
        return AnalyticDistance::class;
    }

    public function toArray(): array{
        return map(
            fn (AnalyticDistance $distance) => [
                'analyticCode' => $distance->analyticCode,
                'totalDistanceKm' => $distance->totalDistanceKm,
                'periodStart' => $distance->periodStart,
                'periodEnd' => $distance->periodEnd,
                'group' => $distance->group
            ],
            $this->items()
        );
    }
}
