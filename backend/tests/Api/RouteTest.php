<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RouteTest extends ApiTestCase
{
    private static HttpClientInterface $httpClient;

    protected function setUp(): void
    {
        self::$alwaysBootKernel = true;
        self::$httpClient = ApiTestCase::createClient();
    }

    public function testGetRoutes(): void
    {
        $response = self::$httpClient->request('POST', '/api/v1/routes', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'fromStationId' => 'IO',
                'toStationId' => 'SP',
                'analyticCode' => 'fret'
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = $response->toArray();
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('fromStationId', $response);
        $this->assertArrayHasKey('toStationId', $response);
        $this->assertArrayHasKey('analyticCode', $response);
        $this->assertArrayHasKey('distanceKm', $response);
        $this->assertArrayHasKey('path', $response);
        $this->assertSame(18.01, $response['distanceKm']);
    }

    public function testCalculateRouteWithInvalidAnalyticCode(): void
    {
        $response = self::$httpClient->request('POST', '/api/v1/routes', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'fromStationId' => 'IO',
                'toStationId' => 'SP',
                'analyticCode' => 'INVALID_CODE'
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCalculateRouteWithInvalidStation(): void
    {
        self::$httpClient->request('POST', '/api/v1/routes', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'fromStationId' => 'INVALID_STATION',
                'toStationId' => 'SP',
                'analyticCode' => 'fret'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
