<?php

namespace Xudid\CsrfMiddleware;

use Core\Security\Token;

interface TokenStorageInterface
{
    public function add(Token $token): static;
    public function has(string $token): bool;
    public function burn(string $token): static;

    public function isValid(string $token): bool;

    public function count(): int;
}
