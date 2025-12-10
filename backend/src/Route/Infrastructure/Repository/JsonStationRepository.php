<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\Infrastructure\Repository;

use App\Route\Domain\Entity\Station;
use App\Route\Domain\Exception\StationsFileEmptyException;
use App\Route\Domain\Exception\StationsFileInvalidJsonException;
use App\Route\Domain\Exception\StationsFileNotFoundException;
use App\Route\Domain\Repository\StationRepositoryInterface;
use App\Route\Domain\ValueObject\StationId;
use App\Route\Domain\ValueObject\Stations;

class JsonStationRepository implements StationRepositoryInterface
{
    private Stations $stations;

    public function __construct(public readonly string $stationsFile)
    {
        if (!file_exists($this->stationsFile)) {
            throw new StationsFileNotFoundException($this->stationsFile);
        }

        $content = file_get_contents($this->stationsFile);

        if (false === $content || '' === trim($content)) {
            throw new StationsFileEmptyException($this->stationsFile);
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new StationsFileInvalidJsonException($this->stationsFile, json_last_error_msg());
        }
        $stations = [];
        foreach ($data as $item) {
            $station = new Station(
                id: StationId::fromInt($item['id']),
                shortName: $item['shortName'],
                longName: $item['longName']
            );

            $stations[] = $station;
        }
        $this->stations = new Stations($stations);
    }

    public function findByShortName(string $shortName): ?Station
    {
        return $this->stations->findByShortName($shortName);
    }
}
