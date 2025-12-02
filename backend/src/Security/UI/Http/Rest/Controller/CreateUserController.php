<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\UI\Http\Rest\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Security\UI\Http\Rest\Input\CreateUserInput;
use App\Shared\Domain\Bus\Command\CommandBus;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[AsController]
class CreateUserController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly ValidatorInterface $validator,
    ) {}

    public function __invoke(
        #[MapRequestPayload] CreateUserInput $input
    )
    {
        $this->validator->validate($input);
dd($input);    }
}
