<?php

use Core\Security\Token;
use PHPUnit\Framework\TestCase;
use Xudid\CsrfMiddleware\TokenStorage;
use Xudid\CsrfMiddleware\TokenStorageInterface;

class TokenStorageTest extends TestCase
{
    public function testAdd()
    {
        $storage = new TokenStorage();
        $result = $storage->add(new Token);
        $this->assertInstanceOf(TokenStorageInterface::class, $result);
        $this->assertEquals(1, $storage->count());
    }

    public function testBurn()
    {
        $storage = new TokenStorage();
        $token = new Token();
        $storage->add($token);
        $storage->burn((string)$token);
        $this->assertEquals(0, $storage->count());
    }

    public function testHas()
    {
        $storage = new TokenStorage();
        $token = new Token;
        $token2 = new Token;
        $storage->add($token);
        $this->assertTrue($storage->has($token));
        $this->assertFalse($storage->has($token2));
    }

    public function testDefaultLimit()
    {
        $storage = new TokenStorage();
        $limit = 60;
        $generatedTokens = [];
        for ($i = 1; $i <= $limit; $i++) {
            $token = new Token();
            $generatedTokens[$i] = $token;
            $storage->add($token);
        }
        $this->assertEquals(50, $storage->count());
        $this->assertFalse($storage->has((string)$generatedTokens[10]));
        $this->assertTrue($storage->has((string)$generatedTokens[60]));
    }

    public function testLimit()
    {
        $storage = new TokenStorage();
        $storage->limit(100);
        $limit = 160;
        $generatedTokens = [];
        for ($i = 1; $i <= $limit; $i++) {
            $token = new Token();
            $generatedTokens[$i] = $token;
            $storage->add($token);
        }
        $this->assertEquals(100, $storage->count());
        $this->assertFalse($storage->has((string)$generatedTokens[60]));
        $this->assertTrue($storage->has((string)$generatedTokens[160]));
    }

    public function testIsValid()
    {
        $storage = new TokenStorage();
        $token = new Token();
        $storage->add($token);
        $valid = $storage->isValid($token);
        $this->assertTrue($valid);
        $storage->burn($token);
        $valid = $storage->isValid($token, 5);
        $this->assertFalse($valid);
    }
}
