<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Domain\ValueObject;

class AnalyticDistance {
    public function __construct(
        public string $analyticCode,
        public float $totalDistanceKm,
        public string $periodStart,
        public string $periodEnd,
        public ?string $group = null
    ) {}
}
