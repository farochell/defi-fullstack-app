<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\UI\Http\Rest\V1\Formatter;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Shared\Domain\Exception\ErrorCode;
use Symfony\Component\Validator\ConstraintViolationInterface;

class SymfonyValidationExceptionFormatter
{
    /**
     * @return mixed[]
     */
    public static function format(ValidationException $exception): array
    {
        $details = [];

        foreach ($exception->getConstraintViolationList() as $violation) {
            /** @var ConstraintViolationInterface $violation */
            $propertyPath = $violation->getPropertyPath();
            $message = $violation->getMessage();

            $details[] = $propertyPath
                ? "{$propertyPath}: {$message}"
                : $message;
        }

        return [
            'code' => ErrorCode::MISSING_PARAMETERS,
            'message' => 'Les données suivantes sont manquantes:',
            'details' => $details,
        ];
    }
}
