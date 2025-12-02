<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\UI\Http\Rest\Controller;

use App\Security\Domain\Service\AccessTokenGenerator;
use App\Security\UI\Http\Rest\Input\LoginInput;
use App\Shared\Domain\Bus\Command\CommandBus;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[AsController]
class LoginController {
    public function __construct(
        private CommandBus $commandBus,
        private AccessTokenGenerator $accessTokenGenerator
    )
    {

    }

    public function __invoke(
        #[MapRequestPayload] LoginInput $input
    ) {
        dd($input);
    }
}
