<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Integration\Route\Application;

use App\Route\Application\CalculateRoute\CalculateRouteCommand;
use App\Route\Application\CalculateRoute\CalculateRouteCommandHandler;
use App\Route\Application\CalculateRoute\RouteResponse;
use App\Route\Domain\Exception\StationNotFoundException;
use App\Route\Domain\Repository\RouteRepositoryInterface;
use App\Route\Domain\Repository\StationRepositoryInterface;
use App\Route\Domain\Service\RailNetworkInterface;
use App\Route\Domain\Service\ShortestPathFinderInterface;
use App\Shared\Domain\Bus\Event\EventBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CalculateRouteTest extends KernelTestCase
{
    private ?ContainerInterface $container = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = static ::getContainer();
    }

    protected function tearDown(): void
    {
        $this->container = null;
        parent::tearDown();
    }

    public function testCalculateRoute(): void
    {
        $stationRepo = $this->container->get(StationRepositoryInterface::class);
        $network = $this->container->get(RailNetworkInterface::class);
        $shortestPathFinder = $this->container->get(ShortestPathFinderInterface::class);
        $eventBus = $this->container->get(EventBus::class);
        $handler = new CalculateRouteCommandHandler($stationRepo, $network, $shortestPathFinder, $eventBus);
        $response = $handler(new CalculateRouteCommand('IO', 'SP', 'fret'));
        $this->assertInstanceOf(RouteResponse::class, $response);
        $this->assertNotEmpty($response->path);
    }

    public function testCalculateRouteNotFound(): void
    {
        $stationRepo = $this->container->get(StationRepositoryInterface::class);
        $network = $this->container->get(RailNetworkInterface::class);
        $shortestPathFinder = $this->container->get(ShortestPathFinderInterface::class);
        $eventBus = $this->container->get(EventBus::class);
        $handler = new CalculateRouteCommandHandler($stationRepo, $network, $shortestPathFinder, $eventBus);
        $this->expectException(StationNotFoundException::class);
        $handler(new CalculateRouteCommand('IOS', 'SP', 'fret'));
    }
}
