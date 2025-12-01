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
use PHPUnit\Framework\TestCase;

class DijkstraShortestPathFinderTest extends TestCase
{
    private DijkstraShortestPathFinder $finder;

    protected function setUp(): void
    {
        $this->finder = new DijkstraShortestPathFinder();
    }

    public function testFindShortestPathReturnsCorrectResult(): void {
        $stationA = new Station(id: StationId::fromInt(1), shortName: 'A', longName: 'Station A');
        $stationB = new Station(id: StationId::fromInt(2), shortName: 'B', longName: 'Station B');
        $stationC = new Station(id: StationId::fromInt(3), shortName: 'C', longName: 'Station C');

        // Mock du réseau
        $network = $this->createMock(RailNetworkInterface::class);

        $network->method('getStationByShortName')->willReturnMap([
            ['A', $stationA],
            ['B', $stationB],
            ['C', $stationC]
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

        $this->assertInstanceOf(ShortestPathResult::class, $result);
        $this->assertEquals(
            ['A', 'B', 'C'],
            array_map(fn(Station $s) => $s->shortName, $result->stations->all())
        );
        $this->assertEquals(8, $result->distanceKm);
    }

    public function testThrowsExceptionWhenNoPathFound(): void
    {
        $this->expectException(NoPathFoundException::class);

        $stationA = new Station(id: StationId::fromInt(1), shortName: 'A', longName: 'Station A');
        $stationB = new Station(id: StationId::fromInt(2), shortName: 'B', longName: 'Station B');

        $network = $this->createMock(RailNetworkInterface::class);
        $network->method('getStationByShortName')->willReturnMap([
            ['A', $stationA],
            ['B', $stationB]
        ]);

        $network->method('getOutgoingLinks')->willReturnMap([
            [$stationA, new DistanceLinks([])],
            [$stationB, new DistanceLinks([])]
        ]);

        $this->finder->findShortestPath($network, $stationA, $stationB);
    }

}
