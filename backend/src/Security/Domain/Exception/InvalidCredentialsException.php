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

class InvalidCredentialsException extends DomainException implements ApiExceptionInterface
{
    use ApiExceptionTrait;
    public function __construct()
    {
        parent::__construct(
            message: "Identifiants invalides",
            code: 401
        );
    }

    public function getErrorCode(): ErrorCode {
        return ErrorCode::INVALID_CREDENTIALS;
    }

    public function getDetails(): array {
        return [
            'message' => 'Identifiant ou mot de passe invalide',
        ];
    }
}
