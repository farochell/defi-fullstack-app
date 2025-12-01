<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Domain\ValueObject;

use App\Route\Domain\Entity\Station;

class DistanceLink
{
    public function __construct(
        public Station $from,
        public Station $to,
        public float $distanceKm
    ) {
    }
}
