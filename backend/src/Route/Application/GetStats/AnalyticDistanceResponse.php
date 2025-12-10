<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\Application\GetStats;

use App\Route\Domain\ValueObject\AnalyticDistance;
use App\Shared\Application\SerializableResponse;
use App\Shared\Domain\Bus\Query\QueryResponse;

class AnalyticDistanceResponse extends SerializableResponse implements QueryResponse
{
    public function __construct(public readonly AnalyticDistance $analyticDistance)
    {
    }

    public static function fromDomain(AnalyticDistance $analyticDistance): self
    {
        return new self($analyticDistance);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'analyticCode' => $this->analyticDistance->analyticCode,
            'totalDistanceKm' => $this->analyticDistance->totalDistanceKm,
            'periodStart' => $this->analyticDistance->periodStart,
            'periodEnd' => $this->analyticDistance->periodEnd,
            'group' => $this->analyticDistance->group,
        ];
    }
}
