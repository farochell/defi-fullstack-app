<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Domain\Exception;

use App\Shared\Domain\Exception\ApiExceptionInterface;
use App\Shared\Domain\Exception\ApiExceptionTrait;
use App\Shared\Domain\Exception\ErrorCode;
use DomainException;

class UnAuthorizedException extends DomainException  implements ApiExceptionInterface{

    use ApiExceptionTrait;
    public function __construct()
    {
        parent::__construct(
            message: "Access Forbidden",
            code: 403
        );
    }
    public function getErrorCode(): ErrorCode {
        return ErrorCode::ACCESS_FORBIDDEN;
    }

    public function getDetails(): array {
        return [];
    }

    public function toOpenApiError(): array {
        // TODO: Implement toOpenApiError() method.
    }
}
