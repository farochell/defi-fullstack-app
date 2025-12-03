<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Infrastructure\Service;

use App\Security\Domain\Exception\AuthTokenExpired;
use App\Security\Domain\Exception\AuthTokenInvalid;
use App\Security\Domain\Service\AccessTokenDecoder;
use App\Security\Domain\Service\AccessTokenGenerator;
use App\Security\Domain\ValueObject\AccessToken;
use App\Security\Domain\ValueObject\UserIdentity;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class JWTAccessToken implements AccessTokenGenerator , AccessTokenDecoder{
    private const string ALGORITHM = 'RS256';

    public function __construct(
        private readonly string $privateKeyPath,
        private readonly string $publicKeyPath,
        private readonly ?string $passphrase = null
    ) {}
    public function decode(string $token): AccessToken {
        try {
            $publicKey = file_get_contents($this->publicKeyPath);
            $jwt = JWT::decode($token, new Key($publicKey, self::ALGORITHM));
        } catch (ExpiredException) {
            throw new AuthTokenExpired();
        } catch (Throwable) {
            throw new AuthTokenInvalid();
        }

        if (! $this->isValidPayload($jwt)) {
            throw new AuthTokenInvalid();
        }
        return new AccessToken(
            $token,
            $jwt->id,
            $jwt->username,
            $jwt->roles,
            $jwt->iat,
            $jwt->exp
        );
    }

    public function generate(UserIdentity $userIdentity, int $expiresIn = 3600): string {
        $issuedAt = time();
        $expiresAt = $issuedAt + $expiresIn;

        $payload = [
            'id' => $userIdentity->userId,
            'username' => $userIdentity->username,
            'roles' => $userIdentity->roles,
            'iat' => $issuedAt,
            'exp' => $expiresAt,
        ];
        $privateKeyContent = file_get_contents($this->privateKeyPath);
        $privateKey = openssl_pkey_get_private($privateKeyContent, $this->passphrase);

        if (!$privateKey) {
            throw new \RuntimeException('Invalid private key or passphrase provided.');
        }

        return JWT::encode($payload, $privateKey, self::ALGORITHM);
    }

    private function isValidPayload(object $jwt): bool
    {
        return isset($jwt->username, $jwt->roles) &&  isset($jwt->iat) && is_int($jwt->iat)
            && isset($jwt->exp) && is_int($jwt->exp);
    }
}
