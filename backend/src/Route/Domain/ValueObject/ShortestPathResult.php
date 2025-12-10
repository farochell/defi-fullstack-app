<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\Domain\ValueObject;

use App\Route\Domain\Exception\EmptyPathException;
use App\Route\Domain\Exception\NegativeDistanceException;

class ShortestPathResult
{
    public function __construct(
        public Stations $stations,
        public float $distanceKm,
    ) {
        if ($distanceKm < 0) {
            throw new NegativeDistanceException($distanceKm);
        }

        if ($this->stations->isEmpty()) {
            throw new EmptyPathException();
        }
    }
}
