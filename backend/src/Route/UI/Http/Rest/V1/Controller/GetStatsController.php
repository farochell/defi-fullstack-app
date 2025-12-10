<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\UI\Http\Rest\V1\Controller;

use App\Route\Application\GetStats\GetStatsQuery;
use App\Route\UI\Http\Rest\V1\Formatter\ErrorFormatterTrait;
use App\Route\UI\Http\Rest\V1\Input\GetStatsInput;
use App\Shared\Domain\Bus\Query\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

#[AsController]
class GetStatsController
{
    use ErrorFormatterTrait;

    public function __construct(
        private readonly QueryBus $queryBus,
    ) {
    }

    public function __invoke(
        #[MapQueryString] ?GetStatsInput $analyticInput = null,
    ): JsonResponse {
        try {
            $response = $this->queryBus->ask(
                new GetStatsQuery(
                    $analyticInput?->from,
                    $analyticInput?->to,
                    $analyticInput?->groupBy
                )
            );

            return new JsonResponse($response);
        } catch (\Throwable $e) {
            return $this->formatError($e);
        }
    }
}
