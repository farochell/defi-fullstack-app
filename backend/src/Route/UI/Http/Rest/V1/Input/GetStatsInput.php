<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Route\UI\Http\Rest\V1\Input;

use ApiPlatform\Metadata\ApiProperty;

class GetStatsInput
{
    #[ApiProperty(
        description: 'Date de début (inclus)',
        required: false
    )]
    public ?string $from = null;

    #[ApiProperty(
        description: 'Date de fin (inclus)',
        required: false,
        schema: ['type' => 'string']
    )]
    public ?string $to = null;

    #[ApiProperty(
        description: 'Date de fin (inclus)',
        required: false,
        schema: ['type' => 'string', 'enum' => ['day', 'month', 'year', 'none']]
    )]
    public ?string $groupBy = 'none';
}
