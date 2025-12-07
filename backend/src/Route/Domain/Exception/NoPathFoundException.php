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

class NoPathFoundException extends \DomainException implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct(private readonly string $from, private readonly string $to)
    {
        parent::__construct(
            message: "Trajet non trouvé",
            code: 400
        );
    }

    public function getErrorCode(): ErrorCode {
        return ErrorCode::EMPTY_PATH;
    }

    public function getDetails(): array {
        return [
            'message' => 'Trajet non trouvé entre les stations ' . $this->from . ' et ' . $this->to,
        ];
    }
}
