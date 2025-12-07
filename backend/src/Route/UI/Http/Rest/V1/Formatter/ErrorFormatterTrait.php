<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\UI\Http\Rest\V1\Formatter;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Route\Domain\Exception\InvalidAnalyticCodeException;
use App\Route\Domain\Exception\NoPathFoundException;
use App\Route\Domain\Exception\StationNotFoundException;
use App\Security\Domain\Exception\EmailAlreadyExistException;
use App\Security\Domain\Exception\InvalidCredentialsException;
use App\Shared\Domain\Exception\ErrorCode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

trait ErrorFormatterTrait
{
    public function formatError(Throwable $error): ?JsonResponse
    {
        if (
            $error instanceof StationNotFoundException
            || $error instanceof InvalidAnalyticCodeException
            || $error instanceof EmailAlreadyExistException
            || $error instanceof InvalidCredentialsException
            || $error instanceof NoPathFoundException
        ) {
            return new JsonResponse([
                'code' => $error->getErrorCode(),
                'message' => $error->getMessage(),
                'details' => $error->getDetails(),
            ], $error->getCode());
        }

        if ($error instanceof ValidationException) {
            return new JsonResponse(SymfonyValidationExceptionFormatter::format($error), 400);
        }

        if ($error instanceof HttpException) {
            return new JsonResponse(SymfonyHttpExceptionFormatter::format($error), $error->getStatusCode());
        }

        return new JsonResponse([
            'code' => 'internal_server_error',
            'message' => 'An unexpected error occurred.',
            'details' => [
                $error->getMessage(),
            ],
        ], 500);
    }

    private function getErrorCode(Throwable $error) {

    }
}
