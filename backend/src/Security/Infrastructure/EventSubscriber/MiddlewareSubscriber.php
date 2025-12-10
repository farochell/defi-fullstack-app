<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Infrastructure\EventSubscriber;

use App\Security\Domain\Service\AccessTokenDecoder;
use App\Security\Infrastructure\Context\InMemoryContextService;
use App\Shared\Domain\Exception\ErrorCode;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

readonly class MiddlewareSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AccessTokenDecoder $accessTokenDecoder,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['checkSecurityHeader', 0],
            ],
        ];
    }

    public function checkSecurityHeader(RequestEvent $requestEvent): void
    {
        if ($this->ignoredUri($requestEvent->getRequest())) {
            return;
        }

        $header = $requestEvent->getRequest()->headers->get('authorization');

        // Récupération du token
        if (null === $header) {
            $requestEvent->setResponse(
                new JsonResponse([
                    'code' => ErrorCode::AUTH_MISSING_TOKEN,
                    'message' => 'Bearer token non trouvé',
                    'details' => [],
                ], Response::HTTP_UNAUTHORIZED),
            );

            return;
        }

        $token = str_replace('Bearer ', '', $header);
        try {
            $user = $this->accessTokenDecoder->decode($token);
            InMemoryContextService::set('user', $user);
            $requestEvent->getRequest()->attributes->set('user', $user);
            $memoryContextService = new InMemoryContextService();
        } catch (\Exception) {
            $requestEvent->setResponse(
                new JsonResponse([
                    'code' => ErrorCode::AUTH_FORBIDDEN,
                    'message' => 'Accès non autorisé',
                    'details' => [],
                ], Response::HTTP_FORBIDDEN),
            );

            return;
        }
    }

    private function ignoredUri(Request $request): bool
    {
        $uri = rtrim(
            substr(
                $request->getRequestUri(),
                strlen($request->getBaseUrl(),),
            ),
            '/',
        );

        return '/' === $uri
            || str_starts_with($uri, '/api/login')
            || str_starts_with($uri, '/api/users/create')
            || str_starts_with($uri, '/api/v1/routes')
            || str_starts_with($uri, '/api/docs')
            || str_starts_with($uri, '/_profiler/')
            || str_starts_with($uri, '/_wdt/')
            || str_starts_with($uri, '/_fragment/');
    }
}
