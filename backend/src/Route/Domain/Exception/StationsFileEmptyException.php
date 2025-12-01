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

class StationsFileEmptyException extends RepositoryException implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct(private string $path)
    {
        parent::__construct(
            message: "Stations file is empty",
            code: 500
        );
    }

    public function getErrorCode(): ErrorCode
    {
        return ErrorCode::STATIONS_FILE_EMPTY;
    }

    public function getDetails(): array
    {
        return [$this->path];
    }
}
