<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Domain\Exception;

use App\Shared\Domain\Exception\ApiExceptionInterface;
use App\Shared\Domain\Exception\ApiExceptionTrait;
use App\Shared\Domain\Exception\ErrorCode;
use DomainException;

class InvalidAnalyticCodeException  extends DomainException  implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct(private readonly string $analyticCode)
    {
        parent::__construct(
            message: "Code analytique invalide",
            code: 500
        );
    }
    public function getErrorCode(): ErrorCode {
        return ErrorCode::INVALID_ANALYTIC_CODE;
    }

    public function getDetails(): array {
        return ['analyticCode' => $this->analyticCode];
    }
}
