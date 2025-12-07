<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace Unit\Route\Domain\Exception;

use App\Route\Domain\Exception\EmptyPathException;
use App\Shared\Domain\Exception\ApiExceptionInterface;
use App\Shared\Domain\Exception\ErrorCode;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class EmptyPathExceptionTest extends TestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new EmptyPathException();

        $this->assertInstanceOf(EmptyPathException::class, $exception);
    }

    public function testExceptionExtendsRuntimeException(): void
    {
        $exception = new EmptyPathException();

        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

    public function testExceptionImplementsApiExceptionInterface(): void
    {
        $exception = new EmptyPathException();

        $this->assertInstanceOf(ApiExceptionInterface::class, $exception);
    }

    public function testExceptionHasCorrectMessage(): void
    {
        $exception = new EmptyPathException();

        $this->assertSame(
            'Le chemin est vide.',
            $exception->getMessage()
        );
    }

    public function testExceptionHasCorrectCode(): void
    {
        $exception = new EmptyPathException();

        $this->assertSame(400, $exception->getCode());
    }

    public function testGetErrorCodeReturnsCorrectEnum(): void
    {
        $exception = new EmptyPathException();

        $this->assertSame(
            ErrorCode::EMPTY_PATH,
            $exception->getErrorCode()
        );
    }

    public function testGetDetailsReturnsEmptyArray(): void
    {
        $exception = new EmptyPathException();

        $details = $exception->getDetails();

        $this->assertIsArray($details);
        $this->assertEmpty($details);
        $this->assertCount(0, $details);
    }

    public function testGetDetailsStructure(): void
    {
        $exception = new EmptyPathException();

        $details = $exception->getDetails();

        $this->assertEquals([], $details);
    }
}
