<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\Domain\Repository;

use App\Route\Domain\Entity\Route;
use App\Route\Domain\ValueObject\AnalyticDistances;
use App\Route\Domain\ValueObject\GroupBy;

interface RouteRepositoryInterface
{
    public function save(Route $route): void;

    public function getAnalyticDistances(
        ?\DateTimeImmutable $from,
        ?\DateTimeImmutable $to,
        ?GroupBy $groupBy,
    ): AnalyticDistances;
}
