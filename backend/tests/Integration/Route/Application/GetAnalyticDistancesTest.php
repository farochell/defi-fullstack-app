<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Integration\Route\Application;

use App\Route\Application\GetAnalyticDistances\AnalyticDistancesResponse;
use App\Route\Application\GetAnalyticDistances\GetAnalyticDistancesQuery;
use App\Route\Application\GetAnalyticDistances\GetAnalyticDistancesQueryHandler;
use App\Route\Domain\Repository\RouteRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GetAnalyticDistancesTest extends KernelTestCase
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

    public function testGetAnalyticDistances(): void
    {
        /** @var RouteRepositoryInterface $routeRepo */
        $routeRepo = $this->container->get(RouteRepositoryInterface::class);

        $handler = new GetAnalyticDistancesQueryHandler($routeRepo);

        $query = new GetAnalyticDistancesQuery(
            from: '2024-01-01',
            to: '2024-12-31',
            groupBy: 'month'
        );

        $response = $handler($query);

        // --- Assertions ---
        $this->assertInstanceOf(AnalyticDistancesResponse::class, $response);

        $this->assertSame('2024-01-01', $response->from);
        $this->assertSame('2024-12-31', $response->to);
        $this->assertSame('month', $response->groupBy);
        $this->assertIsArray($response->items);
        $this->assertNotNull($response->items);
    }

    public function testGetAnalyticDistancesWithoutFilters(): void
    {
        /** @var RouteRepositoryInterface $routeRepo */
        $routeRepo = $this->container->get(RouteRepositoryInterface::class);
        $handler = new GetAnalyticDistancesQueryHandler($routeRepo);

        $query = new GetAnalyticDistancesQuery(
            from: null,
            to: null,
            groupBy: null
        );

        $response = $handler($query);

        $this->assertInstanceOf(AnalyticDistancesResponse::class, $response);
        $this->assertNull($response->from);
        $this->assertNull($response->to);
        $this->assertNull($response->groupBy);

        $this->assertIsArray($response->items);
    }

    public function testGetAnalyticDistancesWithInvalidGroupBy(): void
    {
        /** @var RouteRepositoryInterface $routeRepo */
        $routeRepo = $this->container->get(RouteRepositoryInterface::class);
        $handler = new GetAnalyticDistancesQueryHandler($routeRepo);

        $query = new GetAnalyticDistancesQuery(
            from: '2024-01-01',
            to: '2024-02-01',
            groupBy: 'INVALID_GROUP'
        );

        $response = $handler($query);

        $this->assertInstanceOf(AnalyticDistancesResponse::class, $response);
        $this->assertNull($response->groupBy);
        $this->assertIsArray($response->items);
    }
}
