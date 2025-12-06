<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\UI\Http\Rest\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use App\Security\UI\Http\Rest\Controller\LoginController;
use App\Security\UI\Http\Rest\Input\LoginInput;
use ArrayObject;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/login',
            inputFormats: ['json' => ['application/json']],
            status: 201,
            controller: LoginController::class,
            openapi: new Operation(
                tags: ['Authentification'],
                responses: [
                    '200' => new Response(
                        description: 'Retourne le token JWT',
                        content: new ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]),
                    ),
                    '401' => new Response(
                        description: 'Email ou mot de passe incorrect',
                        content: new ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string'],
                                        'message' => ['type' => 'string'],
                                        'details' => [
                                            'type' => 'array',
                                            'items' => ['type' => 'string']
                                        ],
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '422' => new Response(
                        description: 'Email ou mot de passe manquants',
                        content: new ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string'],
                                        'message' => ['type' => 'string'],
                                        'details' => [
                                            'type' => 'array',
                                            'items' => ['type' => 'string']
                                        ],
                                    ]
                                ]
                            ]
                        ])
                    )
                ],
                summary: 'Identification utilisateur',
                description: 'Identification utilisateur'
            ),
            validationContext: ['groups' => ['login']],
            input: LoginInput::class,
            read: false,
        )
    ]
)]
class LoginResource
{
    #[ApiProperty(
        description: 'Email de l\'utilisateur',
    )]
    public string $email;

    #[ApiProperty(
        description: 'Mot de passe de l\'utilisateur',
    )]
    public string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
}
