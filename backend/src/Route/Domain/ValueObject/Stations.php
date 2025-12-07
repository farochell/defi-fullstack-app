<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Domain\ValueObject;

use App\Route\Domain\Entity\Station;
use App\Shared\Domain\Collection;

use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reindex;

/**
 * @extends Collection<Station>
 */
class Stations extends Collection
{
    /** @var array<string, Station>|null */
    private ?array $indexedStationByShortname = null;

    protected function type(): string
    {
        return Station::class;
    }

    /**
     * @return array<string, Station>
     */
    public function indexedStationByShortname(): array
    {
        if (null === $this->indexedStationByShortname) {
            $this->indexedStationByShortname = reindex(
                static fn (Station $station) => $station->shortName,
                $this->items()
            );
        }

        return $this->indexedStationByShortname;
    }

    public function findByShortName(string $shortName): ?Station
    {
        $values = $this->indexedStationByShortname();

        return $values[$shortName] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return map(
            fn (Station $station) => [
                'id' => $station->id->value(),
                'shortName' => $station->shortName,
                'longName' => $station->longName,
            ],
            $this->items()
        );
    }
}
