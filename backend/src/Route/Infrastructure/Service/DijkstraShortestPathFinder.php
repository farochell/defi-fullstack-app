<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\Infrastructure\Service;

use App\Route\Domain\Entity\Station;
use App\Route\Domain\Exception\NoPathFoundException;
use App\Route\Domain\Exception\StationNotFoundException;
use App\Route\Domain\Service\RailNetworkInterface;
use App\Route\Domain\Service\ShortestPathFinderInterface;
use App\Route\Domain\ValueObject\ShortestPathResult;
use App\Route\Domain\ValueObject\Stations;

class DijkstraShortestPathFinder implements ShortestPathFinderInterface
{
    public function findShortestPath(
        RailNetworkInterface $network,
        Station $from,
        Station $to,
    ): ShortestPathResult {
        $dist = [];
        $prev = [];
        $queue = [];

        $dist[$from->shortName] = 0;
        $queue[$from->shortName] = 0;

        while (!empty($queue)) {
            asort($queue);
            $currentShortName = array_key_first($queue);
            unset($queue[$currentShortName]);

            if ($currentShortName === $to->shortName) {
                break;
            }

            $currentStation = $network->getStationByShortName($currentShortName);
            if (!$currentStation) {
                throw new StationNotFoundException($currentShortName);
            }

            foreach ($network->getOutgoingLinks($currentStation) as $link) {
                $neighbor = $link->to;
                $newDist = $dist[$currentShortName] + $link->distanceKm;
                if (!isset($dist[$neighbor->shortName]) || $newDist < $dist[$neighbor->shortName]) {
                    $dist[$neighbor->shortName] = $newDist;
                    $prev[$neighbor->shortName] = $currentStation;
                    $queue[$neighbor->shortName] = $newDist;
                }
            }
        }

        if (!isset($dist[$to->shortName])) {
            throw new NoPathFoundException($from->shortName, $to->shortName);
        }

        $path = [];
        $curr = $to;

        while (null !== $curr) {
            array_unshift($path, $curr);
            $curr = $prev[$curr->shortName] ?? null;
        }

        return new ShortestPathResult(
            stations: new Stations($path),
            distanceKm: $dist[$to->shortName],
        );
    }
}
