<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

class EntityNotFoundException extends RepositoryException implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct(
        public string $entityName,
        public string $identifier,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            sprintf('Entity %s with ID %s not found', $entityName, $identifier),
            404,
            $previous
        );
    }

    public static function withId(string $entityName, string $id): self
    {
        return new self($entityName, $id);
    }

    public function getErrorCode(): ErrorCode
    {
        return ErrorCode::ENTITY_NOT_FOUND;
    }

    public function getDetails(): array
    {
        return [$this->entityName, $this->identifier];
    }
}
