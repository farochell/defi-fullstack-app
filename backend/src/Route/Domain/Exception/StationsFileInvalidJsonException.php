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
use App\Shared\Domain\Exception\RepositoryException;

class StationsFileInvalidJsonException extends RepositoryException implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct(private readonly string $path, private readonly string $jsonError)
    {
        parent::__construct(
            message: "Le fichier stations.json n'est pas au format JSON valide. ",
            code: 500
        );
    }

    public function getErrorCode(): ErrorCode
    {
        return ErrorCode::STATIONS_FILE_INVALID_JSON;
    }

    public function getDetails(): array
    {
        return [
            'chemin ' => $this->path,
            'erreur' => $this->jsonError,
        ];
    }
}
