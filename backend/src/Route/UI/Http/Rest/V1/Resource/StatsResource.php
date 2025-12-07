<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\UI\Http\Rest\V1\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\Model\Response;
use App\Route\UI\Http\Rest\V1\Controller\GetAnalyticDistancesController;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/stats/distances',
            routePrefix: '/v1',
            status: 200,
            controller: GetAnalyticDistancesController::class,
            openapi: new Operation(
                operationId: 'getAnalyticDistances',
                tags: ['Analytics'],
                responses: [
                    '200' => new Response(
                        description: 'Agrégations de distances',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'from' => ['type' => 'string', 'format' => 'date'],
                                        'to' => ['type' => 'string', 'format' => 'date'],
                                        'groupBy' => [
                                            'type' => 'string',
                                            'enum' => ['day', 'month', 'year', 'none'],
                                            'default' => 'none',
                                        ],
                                        'items' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'analyticCode' => [
                                                        'type' => 'string',
                                                        'required' => true,
                                                    ],
                                                    'totalDistanceKm' => [
                                                        'type' => 'number',
                                                        'format' => 'float',
                                                        'minimum' => 0,
                                                        'required' => true,
                                                    ],
                                                    'periodStart' => ['type' => 'string', 'format' => 'date'],
                                                    'periodEnd' => ['type' => 'string', 'format' => 'date'],
                                                    'group' => [
                                                        'type' => 'string',
                                                        'description' => 'Unité de groupement si groupBy est utilisé (ex. 2025-11 pour month)',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'items' => ['type' => 'object'],
                                ],
                            ],
                        ])
                    ),
                    '400' => new Response(
                        description: 'Paramètres invalides (ex. from > to)',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string'],
                                        'message' => ['type' => 'string'],
                                        'details' => ['type' => 'array', 'items' => ['type' => 'string']],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ],
                summary: 'BONUS : Distances agrégées par code analytique', description: 'Retourne la somme des distances parcourues par code analytique sur une
                                   période donnée. Si aucune période n’est fournie, utilise la période
                                   complète disponible.',
                parameters: [
                    new Parameter(
                        name: 'from',
                        in: 'query',
                        description: 'Date de début (inclus)',
                        required: false,
                        schema: ['type' => 'string', 'format' => 'date']
                    ),
                    new Parameter(
                        name: 'to',
                        in: 'query',
                        description: 'Date de fin (inclus)',
                        required: false,
                        schema: ['type' => 'string', 'format' => 'date']
                    ),
                    new Parameter(
                        name: 'groupBy',
                        in: 'query',
                        description: 'Optionnel, groupement additionnel',
                        required: false,
                        schema: ['type' => 'string', 'enum' => ['day', 'month', 'year', 'none']]
                    ),
                ],
            ),

            paginationEnabled: false,
            output: false,
            read: false,
            name: 'get_stats_distances'
        ),
    ]
)]
class StatsResource
{
}
