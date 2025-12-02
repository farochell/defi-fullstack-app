<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Unit\Route\Infrastructure\Service;

use App\Route\Domain\Entity\Station;
use App\Route\Domain\Exception\DistancesFileEmptyException;
use App\Route\Domain\Exception\DistancesFileInvalidJsonException;
use App\Route\Domain\Exception\DistancesFileNotFoundException;
use App\Route\Domain\Exception\StationNotFoundException;
use App\Route\Domain\Repository\StationRepositoryInterface;
use App\Route\Domain\ValueObject\StationId;
use App\Route\Infrastructure\Service\JsonRailNetwork;
use App\Tests\Shared\Faker\StationFaker;
use PHPUnit\Framework\TestCase;

class JsonRailNetworkTest extends TestCase {
    private ?string $tmpFile = null;

    public function testThrowsExceptionIfFileNotFound(): void {
        $this->expectException(DistancesFileNotFoundException::class);

        $mockRepo = $this->createMock(StationRepositoryInterface::class);
        new JsonRailNetwork('/path/to/nonexistent/file.json', $mockRepo);
    }

    public function testThrowsExceptionIfFileEmpty(): void {
        $this->expectException(DistancesFileEmptyException::class);

        $file = tempnam(sys_get_temp_dir(), 'empty_');
        file_put_contents($file, '');

        $mockRepo = $this->createMock(StationRepositoryInterface::class);
        new JsonRailNetwork($file, $mockRepo);

        unlink($file);
    }

    public function testThrowsExceptionIfInvalidJson(): void {
        $this->expectException(DistancesFileInvalidJsonException::class);

        $file = tempnam(sys_get_temp_dir(), 'invalid_');
        file_put_contents($file, '{invalid-json');

        $mockRepo = $this->createMock(StationRepositoryInterface::class);
        new JsonRailNetwork($file, $mockRepo);

        unlink($file);
    }

    public function testThrowsExceptionIfStationNotFound(): void {
        $this->expectException(StationNotFoundException::class);

        $jsonData = [
            [
                'distances' => [
                    ['parent' => 'A', 'child' => 'B', 'distance' => 10],
                ],
            ],
        ];
        $file = $this->createTempJsonFile($jsonData);

        $mockRepo = $this->createMock(StationRepositoryInterface::class);
        $mockRepo->method('findByShortName')->willReturn(null);

        new JsonRailNetwork($file, $mockRepo);
    }

    private function createTempJsonFile(array $data): string {
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'distances_').'.json';
        file_put_contents($this->tmpFile, json_encode($data));

        return $this->tmpFile;
    }

    public function testGetOutgoingLinksReturnsCorrectLinks(): void {
        $stationA = StationFaker::createStation();
        $stationB = StationFaker::createStation();

        $jsonData = [
            [
                'distances' => [
                    [
                        'parent' => $stationA->shortName,
                        'child' => $stationB->shortName,
                        'distance' => 12.5
                    ],
                ],
            ],
        ];
        $file = $this->createTempJsonFile($jsonData);
        $mockRepo = $this->createMock(StationRepositoryInterface::class);
        $mockRepo->method('findByShortName')->willReturnMap([
            [$stationA->shortName, $stationA],
            [$stationB->shortName, $stationB],
        ]);

        $network = new JsonRailNetwork($file, $mockRepo);

        $outgoingLinks = $network->getOutgoingLinks($stationA);

        $this->assertCount(1, $outgoingLinks->getIterator());
        $link = $outgoingLinks->getIterator()->current();
        $this->assertSame($stationA, $link->from);
        $this->assertSame($stationB, $link->to);
        $this->assertSame(12.5, $link->distanceKm);
    }

    public function testGetStationByShortNameReturnsStation(): void {
        $stationA = StationFaker::createStation();
        $mockRepo = $this->createMock(StationRepositoryInterface::class);
        $mockRepo->method('findByShortName')->with($stationA->shortName)->willReturn($stationA);

        $file = $this->createTempJsonFile([]);

        $network = new JsonRailNetwork($file, $mockRepo);

        $this->assertSame($stationA, $network->getStationByShortName($stationA->shortName));
    }

    protected function tearDown(): void {
        if ($this->tmpFile !== null && file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }
}
