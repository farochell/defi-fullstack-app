<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Domain\Exception;

use App\Shared\Domain\Exception\ApiExceptionInterface;
use App\Shared\Domain\Exception\ApiExceptionTrait;
use App\Shared\Domain\Exception\ErrorCode;

class EmailAlreadyExistException extends \DomainException implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct()
    {
        parent::__construct(
            message: 'Email déjà utilisé',
            code: 409
        );
    }

    public function getErrorCode(): ErrorCode
    {
        return ErrorCode::EMAIL_ALREADY_EXISTS;
    }

    public function getDetails(): array
    {
        return [];
    }
}
