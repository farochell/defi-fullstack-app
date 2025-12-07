<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Application\CalculateRoute;

use App\Route\Domain\Entity\Station;
use App\Shared\Application\SerializableResponse;
use App\Shared\Domain\Bus\Command\CommandResponse;

class StationResponse extends SerializableResponse implements CommandResponse
{
    public function __construct(public readonly Station $station) {

    }

    public static function fromDomain(Station $station): self {
        return new self($station);
    }

    public function jsonSerialize(): mixed {
        return [
            'id' => $this->station->id->value(),
            'shortName' => $this->station->shortName,
            'longName' => $this->station->longName,
        ];
    }
}
