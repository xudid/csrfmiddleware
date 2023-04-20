<?php

namespace Xudid\CsrfMiddleware;

use ArrayAccess;

interface TokenStorageInterface
{
    public function add(string $token): static;
    public function has(string $token): bool;
    public function burn(string $token): static;

    public function count(): int;
}