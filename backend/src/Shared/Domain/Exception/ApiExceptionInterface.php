<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

interface ApiExceptionInterface
{
    public function getErrorCode(): ErrorCode;

    /**
     * @return mixed[]
     */
    public function getDetails(): array;

    /**
     * @return mixed[]
     */
    public function toOpenApiError(): array;
}
