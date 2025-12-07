<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace Unit\Route\Domain\Exception;

use App\Route\Domain\Exception\DistancesFileNotFoundException;
use App\Shared\Domain\Exception\ApiExceptionInterface;
use App\Shared\Domain\Exception\ErrorCode;
use App\Shared\Domain\Exception\RepositoryException;
use PHPUnit\Framework\TestCase;

class DistancesFileNotFoundExceptionTest extends TestCase
{
    private string $testPath;

    protected function setUp(): void
    {
        $this->testPath = '/var/data/distances.json';
    }

    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new DistancesFileNotFoundException($this->testPath);

        $this->assertInstanceOf(DistancesFileNotFoundException::class, $exception);
    }

    public function testExceptionExtendsRepositoryException(): void
    {
        $exception = new DistancesFileNotFoundException($this->testPath);

        $this->assertInstanceOf(RepositoryException::class, $exception);
    }

    public function testExceptionImplementsApiExceptionInterface(): void
    {
        $exception = new DistancesFileNotFoundException($this->testPath);

        $this->assertInstanceOf(ApiExceptionInterface::class, $exception);
    }

    public function testExceptionHasCorrectMessage(): void
    {
        $exception = new DistancesFileNotFoundException($this->testPath);

        $this->assertSame(
            "Le fichier distances.json n'existe pas.",
            $exception->getMessage()
        );
    }

    public function testExceptionHasCorrectCode(): void
    {
        $exception = new DistancesFileNotFoundException($this->testPath);

        $this->assertSame(500, $exception->getCode());
    }

    public function testGetErrorCodeReturnsCorrectEnum(): void
    {
        $exception = new DistancesFileNotFoundException($this->testPath);

        $this->assertSame(
            ErrorCode::DISTANCES_FILE_NOT_FOUND,
            $exception->getErrorCode()
        );
    }

    public function testGetDetailsReturnsPath(): void
    {
        $exception = new DistancesFileNotFoundException($this->testPath);

        $details = $exception->getDetails();

        $this->assertIsArray($details);
        $this->assertArrayHasKey('chemin', $details);
        $this->assertSame($this->testPath, $details['chemin']);
    }

    public function testGetDetailsStructure(): void
    {
        $exception = new DistancesFileNotFoundException($this->testPath);

        $details = $exception->getDetails();

        $this->assertCount(1, $details);
        $this->assertEquals(
            ['chemin' => $this->testPath],
            $details
        );
    }
}
