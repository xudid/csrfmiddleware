<?php

namespace Xudid\CsrfMiddleware;

use Core\Security\Token;

class TokenStorage implements TokenStorageInterface
{
    private array $tokens = [];
    private int $limit = 50;

    public function __construct($tokens = [])
    {
        $this->tokens = $tokens;
    }

    public function add(Token $token): static
    {
        if ($this->count() == $this->limit) {
            array_shift($this->tokens);
        }

        $this->tokens[] = $token;
        return $this;
    }

    public function has(string $requestToken): bool
    {
        foreach ($this->tokens as $token) {
            if ((string)$token == $requestToken) {
                return true;
            }
        }
        return false;
    }

    public function burn(string $requestToken): static
    {
        foreach ($this->tokens as $index => $token) {
            if ((string)$token == $requestToken) {
                unset($this->tokens[$index]);
                return $this;
            }
        }
        return $this;
    }

    /**
     * If no validity delay is done return storage has $token value
     * If validity delay is done return storage has $token value and $token is not expired
     */
    public function isValid(string $tokenString, ?int $validityDelay = null): bool
    {
        if (!$this->has($tokenString)) {
            return false;
        }

        $token = $this->get($tokenString);

        if (is_null($validityDelay)) {
            return true;
        }

        return $token->isExpired($validityDelay);
    }

    public function count(): int
    {
        return count($this->tokens);
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    private function get(string $tokenString): ?Token
    {
        foreach ($this->tokens as $token) {
            if ((string)$token == $tokenString) {
                return $token;
            }
        }
        return null;
    }
}
