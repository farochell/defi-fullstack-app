<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Domain\Exception;

use App\Shared\Domain\Exception\ApiExceptionInterface;
use App\Shared\Domain\Exception\ApiExceptionTrait;
use App\Shared\Domain\Exception\ErrorCode;

class EmptyPathException extends \RuntimeException implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct()
    {
        parent::__construct(
            message: 'Le chemin est vide.',
            code: 400
        );
    }

    public function getErrorCode(): ErrorCode
    {
        return ErrorCode::EMPTY_PATH;
    }

    public function getDetails(): array
    {
        return [];
    }
}
