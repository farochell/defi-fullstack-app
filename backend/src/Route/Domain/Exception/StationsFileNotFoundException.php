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
use App\Shared\Domain\Exception\RepositoryException;

class StationsFileNotFoundException extends RepositoryException implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct(private readonly string $path)
    {
        parent::__construct(
            message: "Fichier stations.json introuvable.",
            code: 500
        );
    }

    public function getErrorCode(): ErrorCode
    {
        return ErrorCode::STATIONS_FILE_NOT_FOUND;
    }

    public function getDetails(): array
    {
        return ['chemin' =>$this->path];
    }
}
