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
use RuntimeException;

class NegativeDistanceException extends RuntimeException implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct(private readonly float $distance)
    {
        parent::__construct(
            message: "La distance ne peut pas être négative.",
            code: 400
        );
    }

    public function getErrorCode(): ErrorCode
    {
        return ErrorCode::NEGATIVE_DISTANCE;
    }

    public function getDetails(): array
    {
        return ['distance' => $this->distance];
    }
}
