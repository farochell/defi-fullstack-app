<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\Depends;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Faker\Factory;

class SecurityTest extends ApiTestCase
{
    private static HttpClientInterface $httpClient;

    protected function setUp(): void
    {
        self::$alwaysBootKernel = true;
        self::$httpClient = ApiTestCase::createClient();
    }

    public function testCreateUser(): array
    {
        $email = Factory::create()->email();
        $password = Factory::create()->password();
        $response = self::$httpClient->request('POST', '/api/users/create', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' =>$password
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        $response = $response->toArray();
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('email', $response);

        return [$email, $password];
    }

    public function testLogin(): string
    {
        $email = Factory::create()->email();
        $password = Factory::create()->password();
         self::$httpClient->request('POST', '/api/users/create', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' =>$password
            ]
        ]);
        $response = self::$httpClient->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' =>$password
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = $response->toArray();
        $this->assertArrayHasKey('token', $response);
        return $response['token'];
    }
}
