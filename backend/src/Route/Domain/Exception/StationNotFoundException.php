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
use App\Shared\Domain\Exception\RepositoryException;

class StationNotFoundException extends RepositoryException implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct(private readonly string $msg)
    {
        parent::__construct(
            message: "Station not found",
            code: 500
        );
    }

    public function getErrorCode(): ErrorCode {
        return ErrorCode::STATION_NOT_FOUND;
    }

    public function getDetails(): array {
        return [$this->msg];
    }
}
