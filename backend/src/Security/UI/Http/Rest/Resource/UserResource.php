<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\UI\Http\Rest\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Security\UI\Http\Rest\Controller\CreateUserController;
use App\Security\UI\Http\Rest\Input\CreateUserInput;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/users',
            inputFormats: ['json' => ['application/json']],
            status: 201,
            controller: CreateUserController::class,
            openapi: new Operation(
                summary: 'Créer un utilisateur',
                description: 'Créer un utilisateur',
            ),
            input: CreateUserInput::class,
        )
    ],
)]
class UserResource {}
