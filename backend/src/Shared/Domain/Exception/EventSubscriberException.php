<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

class EventSubscriberException extends DomainError implements ApiExceptionInterface
{
    use ApiExceptionTrait;

    public function __construct(public string $msg)
    {
        parent::__construct($this->msg);
    }

    public function getErrorCode(): ErrorCode
    {
        return ErrorCode::EVENT_SUBSCRIBER_EXCEPTION;
    }

    public function getDetails(): array
    {
        return [
            'message' => $this->msg,
        ];
    }
}
