<?php
declare(strict_types=1);

namespace App\Application\JwtGenerator;

use Assert\Assertion;
use DateInterval;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;

class JwtGenerator
{
    public function __construct(private Configuration $configuration, private DateInterval $inactivityTimeout)
    {
    }

    public function generateToken(int $userId, string $issuer, string $role): Token
    {
        $now = new DateTimeImmutable();
        return $this->configuration->builder()
            ->issuedBy($issuer)
            ->issuedAt($now)
            ->expiresAt($now->add($this->inactivityTimeout))
            ->withClaim('userId', $userId)
            ->withClaim('role', $role)
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
        return !$token->isExpired(new DateTimeImmutable());
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function assertToken(Token $token): bool
    {
        Assertion::true(!$token->isExpired(new DateTimeImmutable()));
    }
}
