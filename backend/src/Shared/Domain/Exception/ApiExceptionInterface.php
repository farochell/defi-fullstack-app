<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

interface ApiExceptionInterface
{
    public function getErrorCode(): ErrorCode;

    /**
     * @return string[]
     */
    public function getDetails(): array;

    public function toOpenApiError(): array;
}
