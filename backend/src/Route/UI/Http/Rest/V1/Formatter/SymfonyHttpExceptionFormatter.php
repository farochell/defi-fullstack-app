<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\UI\Http\Rest\V1\Formatter;

use App\Shared\Domain\Exception\ErrorCode;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SymfonyHttpExceptionFormatter
{
    /**
     * @return array<string, mixed>
     */
    public static function format(HttpException $exception): array
    {
        return [
            'code' => ErrorCode::toString($exception->getStatusCode()),
            'message' => $exception->getMessage(),
            'details' => [],
        ];
    }
}
