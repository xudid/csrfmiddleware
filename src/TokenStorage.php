<?php

namespace Xudid\CsrfMiddleware;

class TokenStorage implements TokenStorageInterface
{
    private array $tokens = [];

    public function __construct($tokens = [])
    {
        $this->tokens = $tokens;
    }

    public function add(string $token): static
    {
       $this->tokens[] = $token;
       return $this;
    }

    public function has(string $requestToken): bool
    {
        return in_array($requestToken, $this->tokens);
    }

    public function burn(string $requestToken): static
    {
        foreach ($this->tokens as $index => $token) {
            if ($token == $requestToken) {
                unset($this->tokens[$index]);
                return $this;
            }
        }
        return $this;
    }

    public function count(): int
    {
        return count($this->tokens);
    }
}
