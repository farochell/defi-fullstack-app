<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Application\GetAnalyticDistances;

use App\Route\Domain\Repository\RouteRepositoryInterface;
use App\Route\Domain\ValueObject\GroupBy;
use App\Shared\Domain\Bus\Query\QueryHandler;

class GetAnalyticDistancesQueryHandler implements QueryHandler
{
    public function __construct(public readonly RouteRepositoryInterface $routeRepository)
    {
    }

    public function __invoke(GetAnalyticDistancesQuery $query): AnalyticDistancesResponse
    {
        $from = $query->from ? new \DateTimeImmutable($query->from) : null;
        $to = $query->to ? new \DateTimeImmutable($query->to) : null;

        $groupBy = $query->groupBy ? GroupBy::tryFrom($query->groupBy) : null;

        $distances = $this->routeRepository->getAnalyticDistances($from, $to, $groupBy);

        return AnalyticDistancesResponse::fromDomain(
            $from?->format('Y-m-d'),
            $to?->format('Y-m-d'),
            $groupBy?->value,
            $distances
        );
    }
}
