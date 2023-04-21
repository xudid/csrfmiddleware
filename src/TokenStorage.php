<?php

namespace Xudid\CsrfMiddleware;

class TokenStorage implements TokenStorageInterface
{
    private array $tokens = [];
    private int $limit = 50;

    public function __construct($tokens = [])
    {
        $this->tokens = $tokens;
    }

    public function add(string $token): static
    {
        if ($this->count() == $this->limit){
            array_shift($this->tokens);
        }

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

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }
}
