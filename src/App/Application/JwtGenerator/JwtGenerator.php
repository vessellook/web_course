<?php
declare(strict_types=1);

namespace App\Application\JwtGenerator;

use DateInterval;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;

class JwtGenerator
{
    public function __construct(private Configuration $configuration)
    {
    }

    public function generateToken(int $userId, string $issuer, string $role): Token
    {
        $now = new DateTimeImmutable();
        return $this->configuration->builder()
            ->issuedBy($issuer)
            ->issuedAt($now)
            ->expiresAt($now->add(new DateInterval('PT2M'))) // +2 minutes
            ->withHeader('userId', $userId)
            ->withHeader('role', $role)
            ->getToken(
                $this->configuration->signer(),
                $this->configuration->signingKey()
            );
    }

    public function parseToken(string $token): Token
    {
        return $this->configuration->parser()->parse($token);
    }

    public function validateToken(Token $token): bool
    {
        $constraints = $this->configuration->validationConstraints();
        return $this->configuration->validator()->validate($token, ...$constraints);
    }
}
