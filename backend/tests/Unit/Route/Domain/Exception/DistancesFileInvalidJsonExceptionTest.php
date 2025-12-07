<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace Unit\Route\Domain\Exception;

use App\Route\Domain\Exception\DistancesFileInvalidJsonException;
use App\Shared\Domain\Exception\ApiExceptionInterface;
use App\Shared\Domain\Exception\ErrorCode;
use App\Shared\Domain\Exception\RepositoryException;
use PHPUnit\Framework\TestCase;

class DistancesFileInvalidJsonExceptionTest extends TestCase
{
    private string $testPath;
    private string $testJsonError;

    protected function setUp(): void
    {
        $this->testPath = '/var/data/distances.json';
        $this->testJsonError = 'Syntax error';
    }

    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new DistancesFileInvalidJsonException(
            $this->testPath,
            $this->testJsonError
        );

        $this->assertInstanceOf(DistancesFileInvalidJsonException::class, $exception);
    }

    public function testExceptionExtendsRepositoryException(): void
    {
        $exception = new DistancesFileInvalidJsonException(
            $this->testPath,
            $this->testJsonError
        );

        $this->assertInstanceOf(RepositoryException::class, $exception);
    }

    public function testExceptionImplementsApiExceptionInterface(): void
    {
        $exception = new DistancesFileInvalidJsonException(
            $this->testPath,
            $this->testJsonError
        );

        $this->assertInstanceOf(ApiExceptionInterface::class, $exception);
    }

    public function testExceptionHasCorrectMessage(): void
    {
        $exception = new DistancesFileInvalidJsonException(
            $this->testPath,
            $this->testJsonError
        );

        $this->assertSame(
            "Le fichier distances.json n'est pas au format JSON valide.",
            $exception->getMessage()
        );
    }

    public function testExceptionHasCorrectCode(): void
    {
        $exception = new DistancesFileInvalidJsonException(
            $this->testPath,
            $this->testJsonError
        );

        $this->assertSame(500, $exception->getCode());
    }

    public function testGetErrorCodeReturnsCorrectEnum(): void
    {
        $exception = new DistancesFileInvalidJsonException(
            $this->testPath,
            $this->testJsonError
        );

        $this->assertSame(
            ErrorCode::DISTANCES_FILE_INVALID_JSON,
            $exception->getErrorCode()
        );
    }

    public function test_GetDetailsReturnsPathAndError(): void
    {
        $exception = new DistancesFileInvalidJsonException(
            $this->testPath,
            $this->testJsonError
        );

        $details = $exception->getDetails();

        $this->assertIsArray($details);
        $this->assertArrayHasKey('chemin', $details);
        $this->assertArrayHasKey('erreur', $details);
        $this->assertSame($this->testPath, $details['chemin']);
        $this->assertSame($this->testJsonError, $details['erreur']);
    }
}
