<?php

namespace App\Repositories;

use App\Models\User;
use RuntimeException;
use Tymon\JWTAuth\JWT;
use Lcobucci\JWT\Configuration;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Signer\{Hmac\Sha256, Key\InMemory};
use App\Contracts\Repositories\JWTRepositoryInterface;

class JWTRepository implements JWTRepositoryInterface
{
    /**
     * The JWT lib helper.
     *
     * @var \Tymon\JWTAuth\JWT
     */
    private $jwt;

    /**
     * The JWT config.
     *
     * @var \Lcobucci\JWT\Configuration
     */
    protected $config;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->jwt = app(JWT::class);

        /** @var non-empty-string $appKey */
        $appKey = config('app.key');

        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($appKey),
        );
    }

    /**
     * Generates a JWT token from user.
     *
     * @param \App\Models\User $user
     * @return string
     */
    public function tokenize(User $user): string
    {
        return $this->jwt->fromUser($user);
    }

    /**
     * Decode the jwt token.
     *
     * @param string $token
     * @return array<string, mixed>
     */
    public function decode(string $token): array
    {
        /** @var \Lcobucci\JWT\Token\Plain $decodedToken */
        $decodedToken = $this->config->parser()->parse($token);

        $this->config->setValidationConstraints(
            new LooseValidAt(
                SystemClock::fromSystemTimezone(),
            ),
        );

        $constraints = $this->config->validationConstraints();

        if (!$this->config->validator()->validate($decodedToken, ...$constraints)) {
            throw new RuntimeException('Token validation failed');
        }

        return $decodedToken->claims()->all();
    }
}
