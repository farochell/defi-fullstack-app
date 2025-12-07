<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace Unit\Route\Domain\Exception;

use App\Route\Domain\Exception\DistancesFileEmptyException;
use App\Shared\Domain\Exception\ApiExceptionInterface;
use App\Shared\Domain\Exception\ErrorCode;
use App\Shared\Domain\Exception\RepositoryException;
use PHPUnit\Framework\TestCase;

class DistancesFileEmptyExceptionTest extends TestCase
{
    private string $testPath;

    protected function setUp(): void
    {
        $this->testPath = '/var/data/distances.json';
    }

    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new DistancesFileEmptyException($this->testPath);

        $this->assertInstanceOf(DistancesFileEmptyException::class, $exception);
    }

    public function testExceptionExtendsRepositoryException(): void
    {
        $exception = new DistancesFileEmptyException($this->testPath);

        $this->assertInstanceOf(RepositoryException::class, $exception);
    }

    public function testExceptionImplementsApiExceptionInterface(): void
    {
        $exception = new DistancesFileEmptyException($this->testPath);

        $this->assertInstanceOf(ApiExceptionInterface::class, $exception);
    }

    public function testExceptionHasCorrectMessage(): void
    {
        $exception = new DistancesFileEmptyException($this->testPath);

        $this->assertSame(
            'Le fichiers distances.json est vide.',
            $exception->getMessage()
        );
    }

    public function testExceptionHasCorrectCode(): void
    {
        $exception = new DistancesFileEmptyException($this->testPath);

        $this->assertSame(500, $exception->getCode());
    }

    public function testGetErrorCodeReturnsCorrectEnum(): void
    {
        $exception = new DistancesFileEmptyException($this->testPath);

        $this->assertSame(
            ErrorCode::DISTANCES_FILE_EMPTY,
            $exception->getErrorCode()
        );
    }

    public function testGetDetailsReturnsPath(): void
    {
        $exception = new DistancesFileEmptyException($this->testPath);

        $details = $exception->getDetails();

        $this->assertIsArray($details);
        $this->assertArrayHasKey('chemin ', $details);
        $this->assertSame($this->testPath, $details['chemin ']);
    }

}
