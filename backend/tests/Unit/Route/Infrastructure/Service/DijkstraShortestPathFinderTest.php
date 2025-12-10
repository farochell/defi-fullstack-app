<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Unit\Route\Infrastructure\Service;

use App\Route\Domain\Entity\Station;
use App\Route\Domain\Exception\NoPathFoundException;
use App\Route\Domain\Service\RailNetworkInterface;
use App\Route\Domain\ValueObject\DistanceLink;
use App\Route\Domain\ValueObject\DistanceLinks;
use App\Route\Domain\ValueObject\ShortestPathResult;
use App\Route\Domain\ValueObject\StationId;
use App\Route\Infrastructure\Service\DijkstraShortestPathFinder;
use App\Tests\Shared\Faker\StationFaker;
use PHPUnit\Framework\TestCase;

class DijkstraShortestPathFinderTest extends TestCase
{
    private DijkstraShortestPathFinder $finder;

    protected function setUp(): void
    {
        $this->finder = new DijkstraShortestPathFinder();
    }

    public function testFindShortestPathReturnsCorrectResult(): void {
        $stationA = StationFaker::createStation();
        $stationB = StationFaker::createStation();
        $stationC = StationFaker::createStation();

        $network = $this->createMock(RailNetworkInterface::class);

        $network->method('getStationByShortName')->willReturnMap([
            [$stationA->shortName, $stationA],
            [$stationB->shortName, $stationB],
            [$stationC->shortName, $stationC]
        ]);

        $network->method('getOutgoingLinks')->willReturnMap([
            [$stationA, new DistanceLinks([
                new DistanceLink(from: $stationA, to: $stationB, distanceKm: 5),
                new DistanceLink(from: $stationA, to: $stationC, distanceKm: 10),
            ])],
            [$stationB, new DistanceLinks([
                new DistanceLink(from: $stationB, to: $stationC, distanceKm: 3)
            ])],
            [$stationC, new DistanceLinks([])]
        ]);

        $result = $this->finder->findShortestPath($network, $stationA, $stationC);
        $this->assertEquals(
            [$stationA->shortName, $stationB->shortName, $stationC->shortName],
            array_map(fn(Station $s) => $s->shortName, $result->stations->all())
        );
        $this->assertEquals(8, $result->distanceKm);
    }

    public function testThrowsExceptionWhenNoPathFound(): void
    {
        $this->expectException(NoPathFoundException::class);

        $stationA = StationFaker::createStation();
        $stationB = StationFaker::createStation();

        $network = $this->createMock(RailNetworkInterface::class);
        $network->method('getStationByShortName')->willReturnMap([
            [$stationA->shortName, $stationA],
            [$stationB->shortName, $stationB]
        ]);

        $network->method('getOutgoingLinks')->willReturnMap([
            [$stationA, new DistanceLinks([])],
            [$stationB, new DistanceLinks([])]
        ]);

        $this->finder->findShortestPath($network, $stationA, $stationB);
    }

}
