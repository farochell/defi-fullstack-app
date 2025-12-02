<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Application\GetAnalyticDistances;

use App\Shared\Application\SerializableResponse;

class AnalyticDistancesResponse extends SerializableResponse
{
    public function __construct(
        public ?string $from = null,
        public ?string $to = null,
        public ?string $groupBy = null,
        public array $items
    ) {}
    public function jsonSerialize(): mixed {
       return [
           'from' => $this->from,
           'to' => $this->to,
           'groupBy' => $this->groupBy,
           'items' => $this->items
       ];
    }
}
