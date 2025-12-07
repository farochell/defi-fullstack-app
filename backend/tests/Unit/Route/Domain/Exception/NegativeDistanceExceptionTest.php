<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace Unit\Route\Domain\Exception;

use App\Route\Domain\Exception\NegativeDistanceException;
use App\Shared\Domain\Exception\ApiExceptionInterface;
use App\Shared\Domain\Exception\ErrorCode;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class NegativeDistanceExceptionTest extends TestCase {
    private float $testDistance;

    protected function setUp(): void {
        $this->testDistance = -10.5;
    }

    public function testExceptionCanBeInstantiated(): void {
        $exception = new NegativeDistanceException($this->testDistance);

        $this->assertInstanceOf(NegativeDistanceException::class, $exception);
    }

    public function testExceptionCanBeCaughtAsApiException(): void
    {
        try {
            throw new NegativeDistanceException($this->testDistance);
        } catch (ApiExceptionInterface $e) {
            $this->assertInstanceOf(NegativeDistanceException::class, $e);
            $this->assertSame(ErrorCode::NEGATIVE_DISTANCE, $e->getErrorCode());
        }
    }

    public function testHttpStatusCodeIs400(): void
    {
        $exception = new NegativeDistanceException($this->testDistance);

        $this->assertSame(400, $exception->getCode());
    }

    public function testErrorCodeIsClientError(): void
    {
        $exception = new NegativeDistanceException($this->testDistance);

        $this->assertLessThan(500, $exception->getCode());
        $this->assertGreaterThanOrEqual(400, $exception->getCode());
    }

    public function testExceptionExtendsRuntimeException(): void {
        $exception = new NegativeDistanceException($this->testDistance);

        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

    public function testExceptionImplementsApiExceptionInterface(): void {
        $exception = new NegativeDistanceException($this->testDistance);

        $this->assertInstanceOf(ApiExceptionInterface::class, $exception);
    }

    public function testExceptionHasCorrectMessage(): void {
        $exception = new NegativeDistanceException($this->testDistance);

        $this->assertSame(
            'La distance ne peut pas être négative.',
            $exception->getMessage(),
        );
    }

    public function testExceptionHasCorrectCode(): void
    {
        $exception = new NegativeDistanceException($this->testDistance);

        $this->assertSame(400, $exception->getCode());
    }

    public function testGetErrorCodeReturnsCorrectEnum(): void
    {
        $exception = new NegativeDistanceException($this->testDistance);

        $this->assertSame(
            ErrorCode::NEGATIVE_DISTANCE,
            $exception->getErrorCode()
        );
    }


}
