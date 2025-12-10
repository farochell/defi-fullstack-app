<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\UI\Http\Rest\Controller;

use App\Route\UI\Http\Rest\V1\Formatter\ErrorFormatterTrait;
use App\Security\Application\Login\LoginQuery;
use App\Security\Application\Login\LoginResponse;
use App\Security\Domain\Service\AccessTokenGenerator;
use App\Security\Domain\ValueObject\UserIdentity;
use App\Security\UI\Http\Rest\Input\LoginInput;
use App\Shared\Domain\Bus\Query\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[AsController]
class LoginController
{
    use ErrorFormatterTrait;

    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly AccessTokenGenerator $accessTokenGenerator,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload] LoginInput $input,
    ): JsonResponse {
        try {
            /** @var LoginResponse $login */
            $login = $this->queryBus->ask(
                new LoginQuery($input->email, $input->password)
            );
            $userIdentity = new UserIdentity(
                $login->userId,
                $login->username,
                $login->roles
            );

            return JsonResponse::fromJsonString(json_encode([
                'token' => $this->accessTokenGenerator->generate($userIdentity),
            ], JSON_THROW_ON_ERROR));
        } catch (\Throwable $e) {
            return $this->formatError($e);
        }
    }
}
