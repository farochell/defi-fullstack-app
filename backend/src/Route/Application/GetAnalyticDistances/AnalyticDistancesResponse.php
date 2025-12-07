<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Application\GetAnalyticDistances;

use App\Route\Domain\ValueObject\AnalyticDistance;
use App\Route\Domain\ValueObject\AnalyticDistances;
use App\Shared\Application\SerializableResponse;
use App\Shared\Domain\Bus\Query\QueryResponse;

use function Lambdish\Phunctional\map;

class AnalyticDistancesResponse extends SerializableResponse implements QueryResponse
{
    public function __construct(
        public AnalyticDistances $items,
        public ?string $from = null,
        public ?string $to = null,
        public ?string $groupBy = null,
    ) {
    }

    public static function fromDomain(
        ?string $from,
        ?string $to,
        ?string $groupBy,
        AnalyticDistances $items,
    ): self {
        return new self($items, $from, $to, $groupBy);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'groupBy' => $this->groupBy,
            'items' => map(
                fn (AnalyticDistance $distance) => AnalyticDistanceResponse::fromDomain($distance),
                $this->items
            ),
        ];
    }
}
