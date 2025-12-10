<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\Domain\Service;

use App\Route\Domain\Entity\Station;
use App\Route\Domain\ValueObject\DistanceLinks;

interface RailNetworkInterface
{
    public function getOutgoingLinks(Station $station): DistanceLinks;

    public function getStationByShortName(string $shortName): ?Station;
}
