<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\Infrastructure\Service;

use App\Route\Domain\Entity\Station;
use App\Route\Domain\Exception\DistancesFileEmptyException;
use App\Route\Domain\Exception\DistancesFileInvalidJsonException;
use App\Route\Domain\Exception\DistancesFileNotFoundException;
use App\Route\Domain\Exception\StationNotFoundException;
use App\Route\Domain\Repository\StationRepositoryInterface;
use App\Route\Domain\Service\RailNetworkInterface;
use App\Route\Domain\ValueObject\DistanceLink;
use App\Route\Domain\ValueObject\DistanceLinks;

class JsonRailNetwork implements RailNetworkInterface
{
    /** @var array<DistanceLink> */
    private array $links = [];

    public function __construct(
        private readonly string $distancesFile,
        private readonly StationRepositoryInterface $stationRepo,
    ) {
        if (!file_exists($this->distancesFile)) {
            throw new DistancesFileNotFoundException($this->distancesFile);
        }

        $content = file_get_contents($this->distancesFile);

        if (false === $content || '' === trim($content)) {
            throw new DistancesFileEmptyException($this->distancesFile);
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new DistancesFileInvalidJsonException($this->distancesFile, json_last_error_msg());
        }

        foreach ($data as $network) {
            foreach ($network['distances'] as $item) {
                $from = $this->stationRepo->findByShortName($item['parent']);
                $to = $this->stationRepo->findByShortName($item['child']);

                if (!$from || !$to) {
                    throw new StationNotFoundException('Station inconnue dans distances.json : ' . $item['parent'] . ' ou ' . $item['child']);
                }

                $this->links[] = new DistanceLink(
                    from: $from,
                    to: $to,
                    distanceKm: (float) $item['distance']
                );

                $this->links[] = new DistanceLink(
                    from: $to,
                    to: $from,
                    distanceKm: (float) $item['distance']
                );
            }
        }
    }

    public function getOutgoingLinks(Station $station): DistanceLinks
    {
        $filteredStations = array_filter(
            $this->links,
            fn (DistanceLink $link) => $link->from->shortName === $station->shortName
        );

        return new DistanceLinks($filteredStations);
    }

    public function getStationByShortName(string $shortName): ?Station
    {
        return $this->stationRepo->findByShortName($shortName);
    }
}
