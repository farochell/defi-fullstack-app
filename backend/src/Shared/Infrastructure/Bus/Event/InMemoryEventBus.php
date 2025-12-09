<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Shared\Domain\Exception\EventSubscriberException;
use App\Shared\Infrastructure\Bus\CallableFirstParameterExtractor;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class InMemoryEventBus implements EventBus
{
    private readonly MessageBus $messageBus;

    /**
     * @param iterable<callable> $subscribers
     */
    public function __construct(
        iterable $subscribers,
        private readonly LoggerInterface $logger
    ) {
        $this->messageBus = new MessageBus(
            [
            new HandleMessageMiddleware(
                new HandlersLocator(
                    CallableFirstParameterExtractor::forPipedCallables($subscribers)
                )
            ),
        ]);
    }
    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            try {
                $this->messageBus->dispatch($event);
            } catch (NoHandlerForMessageException) {
                // ignore
            } catch (Exception $e) {
                if ($e->getPrevious() instanceof EventSubscriberException) {
                    throw $e->getPrevious();
                }
                $message = $e->getMessage() . ' : ' . $e->getFile() . ' Line ' . $e->getLine();
                $this->logger->error($message, []);
                throw new EventSubscriberException($message);
            }
        }
    }
}
