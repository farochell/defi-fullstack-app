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
}
