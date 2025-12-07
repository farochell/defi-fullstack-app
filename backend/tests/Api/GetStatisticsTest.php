<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\DependsExternal;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetStatisticsTest extends ApiTestCase
{
    private static HttpClientInterface $httpClient;

    protected function setUp(): void
    {
        self::$alwaysBootKernel = true;
        self::$httpClient = ApiTestCase::createClient();
    }

    #[DependsExternal(SecurityTest::class, 'testLogin')]
    public function testGetStatistics(string $token): void
    {
        $response = self::$httpClient->request('GET', '/api/v1/stats/distances', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = $response->toArray();
        $this->assertArrayHasKey('from', $response);
        $this->assertArrayHasKey('to', $response);
        $this->assertArrayHasKey('groupBy', $response);
        $this->assertArrayHasKey('items', $response);
    }

    #[DependsExternal(SecurityTest::class, 'testLogin')]
    public function testGetStatisticsWithFromParameter(string $token): void {
        $fromDate = '2024-01-01';

        $response = self::$httpClient->request('GET', '/api/v1/stats/distances', [
            'query' => ['from' => $fromDate],
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = $response->toArray();
        $this->assertSame($fromDate, $data['from']);
    }

    #[DependsExternal(SecurityTest::class, 'testLogin')]
    public function testGetStatisticsWithDateRange(string $token): void
    {
        $fromDate = '2024-01-01';
        $toDate = '2024-12-31';

        $response = self::$httpClient->request('GET', '/api/v1/stats/distances', [
            'query' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = $response->toArray();
        $this->assertSame($fromDate, $data['from']);
        $this->assertSame($toDate, $data['to']);
    }

    #[DependsExternal(SecurityTest::class, 'testLogin')]
    public function testGetStatisticsWithGroupByDay(string $token): void
    {
        $response = self::$httpClient->request('GET', '/api/v1/stats/distances', [
            'query' => ['groupBy' => 'day'],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = $response->toArray();
        $this->assertSame('day', $data['groupBy']);
        $this->assertIsArray($data['items']);
    }

    #[DependsExternal(SecurityTest::class, 'testLogin')]
    public function testGetStatisticsWithGroupByMonth(string $token): void
    {
        $response = self::$httpClient->request('GET', '/api/v1/stats/distances', [
            'query' => ['groupBy' => 'month'],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = $response->toArray();
        $this->assertSame('month', $data['groupBy']);
    }

    #[DependsExternal(SecurityTest::class, 'testLogin')]
    public function testGetStatisticsWithGroupByYear(string $token): void
    {
        $response = self::$httpClient->request('GET', '/api/v1/stats/distances', [
            'query' => ['groupBy' => 'year'],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = $response->toArray();
        $this->assertSame('year', $data['groupBy']);
    }

    #[DependsExternal(SecurityTest::class, 'testLogin')]
    public function testGetStatisticsWithAllParameters(string $token): void
    {
        $fromDate = '2024-01-01';
        $toDate = '2024-12-31';
        $groupBy = 'month';

        $response = self::$httpClient->request('GET', '/api/v1/stats/distances', [
            'query' => [
                'from' => $fromDate,
                'to' => $toDate,
                'groupBy' => $groupBy,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = $response->toArray();
        $this->assertSame($fromDate, $data['from']);
        $this->assertSame($toDate, $data['to']);
        $this->assertSame($groupBy, $data['groupBy']);
        $this->assertIsArray($data['items']);
    }

    #[DependsExternal(SecurityTest::class, 'testLogin')]
    public function testGetStatisticsItemsStructure(string $token): void
    {
        $response = self::$httpClient->request('GET', '/api/v1/stats/distances', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = $response->toArray();
        $this->assertIsArray($data['items']);

        if (!empty($data['items'])) {
            $firstItem = $data['items'][0];
            $this->assertArrayHasKey('analyticCode', $firstItem);
            $this->assertArrayHasKey('totalDistanceKm', $firstItem);
            $this->assertArrayHasKey('periodStart', $firstItem);
            $this->assertArrayHasKey('periodEnd', $firstItem);
        }
    }

    public function testGetStatisticsWithoutAuthentication(): void
    {
        self::$httpClient->request('GET', '/api/v1/stats/distances', [
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[DependsExternal(SecurityTest::class, 'testLogin')]
    public function testGetStatisticsWithInvalidToken(string $token): void
    {
        self::$httpClient->request('GET', '/api/v1/stats/distances', [
            'headers' => [
                'Authorization' => 'Bearer invalid_token_here',
                'Accept' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

}
