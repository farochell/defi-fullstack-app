<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace Unit\Route\Domain\Exception;

use App\Route\Domain\Exception\NoPathFoundException;
use App\Shared\Domain\Exception\ApiExceptionInterface;
use App\Shared\Domain\Exception\ErrorCode;
use PHPUnit\Framework\TestCase;

class NoPathFoundExceptionTest extends TestCase
{
    private string $testFrom;
    private string $testTo;

    protected function setUp(): void
    {
        $this->testFrom = 'Paris';
        $this->testTo = 'Lyon';
    }

    public function testExceptionCanBeInstantiated(): void {
        $exception = new NoPathFoundException($this->testFrom, $this->testTo);

        $this->assertInstanceOf(NoPathFoundException::class, $exception);
    }

    public function testExceptionImplementsApiExceptionInterface(): void
    {
        $exception = new NoPathFoundException($this->testFrom, $this->testTo);

        $this->assertInstanceOf(ApiExceptionInterface::class, $exception);
    }

    public function testExceptionHasCorrectMessage(): void
    {
        $exception = new NoPathFoundException($this->testFrom, $this->testTo);

        $this->assertSame(
            'Trajet non trouvé',
            $exception->getMessage()
        );
    }

    public function testExceptionHasCorrectCode(): void
    {
        $exception = new NoPathFoundException($this->testFrom, $this->testTo);

        $this->assertSame(400, $exception->getCode());
    }

    public function testGetErrorCodeReturnsCorrectEnum(): void
    {
        $exception = new NoPathFoundException($this->testFrom, $this->testTo);

        $this->assertSame(
            ErrorCode::EMPTY_PATH,
            $exception->getErrorCode()
        );
    }

    public function testGetDetailsReturnsMessageWithStations(): void
    {
        $exception = new NoPathFoundException($this->testFrom, $this->testTo);

        $details = $exception->getDetails();

        $this->assertIsArray($details);
        $this->assertArrayHasKey('message', $details);
        $this->assertSame(
            'Trajet non trouvé entre les stations Paris et Lyon',
            $details['message']
        );
    }


}
