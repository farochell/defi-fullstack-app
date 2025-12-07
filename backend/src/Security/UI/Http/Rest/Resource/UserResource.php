<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\UI\Http\Rest\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use App\Security\UI\Http\Rest\Controller\CreateUserController;
use App\Security\UI\Http\Rest\Input\CreateUserInput;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/users/create',
            inputFormats: ['json' => ['application/json']],
            status: 201,
            controller: CreateUserController::class,
            openapi: new Operation(
                tags: ['Utilisateurs'],
                responses: [
                    '201' => new Response(
                        description: 'Création du compte utilisateur',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'string'],
                                        'email' => ['type' => 'string'],
                                    ],
                                ],
                            ],
                        ])
                    ),
                    '409' => new Response(
                        description: 'Email déjà utilisé',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string'],
                                        'message' => ['type' => 'string'],
                                        'details' => [
                                            'type' => 'array',
                                            'items' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                            ],
                        ])
                    ),
                    '422' => new Response(
                        description: 'Email ou mot de passe manquants',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string'],
                                        'message' => ['type' => 'string'],
                                        'details' => [
                                            'type' => 'array',
                                            'items' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ],
                summary: 'Créer un utilisateur',
                description: 'Créer un utilisateur',
            ),
            input: CreateUserInput::class,
        ),
    ],
)]
class UserResource
{
}
