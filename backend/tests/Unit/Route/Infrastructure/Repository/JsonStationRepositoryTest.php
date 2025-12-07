<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Unit\Route\Infrastructure\Repository;

use App\Route\Domain\Entity\Station;
use App\Route\Domain\Exception\StationsFileEmptyException;
use App\Route\Domain\Exception\StationsFileInvalidJsonException;
use App\Route\Domain\Exception\StationsFileNotFoundException;
use App\Route\Domain\ValueObject\StationId;
use App\Route\Infrastructure\Repository\JsonStationRepository;
use PHPUnit\Framework\TestCase;

class JsonStationRepositoryTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'stations_');

        $data = [
            [
                'id' => 1,
                'shortName' => 'ABC',
                'longName' => 'Station ABC'
            ],
            [
                'id' => 2,
                'shortName' => 'XYZ',
                'longName' => 'Station XYZ'
            ]
        ];

        file_put_contents($this->tempFile, json_encode($data));
    }
    protected function tearDown(): void
    {
        @unlink($this->tempFile);
    }

    public function testFindByShortNameReturnsStation(): void
    {
        $repository = new JsonStationRepository($this->tempFile);

        $station = $repository->findByShortName('ABC');

        $this->assertInstanceOf(Station::class, $station);
        $this->assertEquals(StationId::fromInt(1), $station->id);
        $this->assertEquals('ABC', $station->shortName);
        $this->assertEquals('Station ABC', $station->longName);
    }

    public function testFindByShortNameReturnsNullWhenNotFound(): void
    {
        $repository = new JsonStationRepository($this->tempFile);

        $station = $repository->findByShortName('NON_EXISTENT');

        $this->assertNull($station);
    }

    public function testThrowsExceptionWhenFileNotFound(): void
    {
        $this->expectException(StationsFileNotFoundException::class);

        unlink($this->tempFile);

        new JsonStationRepository($this->tempFile);
    }

    public function testThrowsExceptionWhenFileIsEmpty(): void
    {
        $this->expectException(StationsFileEmptyException::class);

        file_put_contents($this->tempFile, '');

        new JsonStationRepository($this->tempFile);
    }

    public function testThrowsExceptionWhenJsonIsInvalid(): void
    {
        $this->expectException(StationsFileInvalidJsonException::class);

        file_put_contents($this->tempFile, '{json invalide}');

        new JsonStationRepository($this->tempFile);
    }

}
