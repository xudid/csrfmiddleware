<?php

use PHPUnit\Framework\TestCase;
use Xudid\CsrfMiddleware\TokenStorage;
use Xudid\CsrfMiddleware\TokenStorageInterface;

class TokenStorageTest extends TestCase
{
    public function testAdd()
    {
        $storage = new TokenStorage();
        $result = $storage->add('aze');
        $this->assertInstanceOf(TokenStorageInterface::class, $result);
        $this->assertEquals(1, $storage->count());
    }

    public function testBurn()
    {
        $storage = new TokenStorage();
        $storage->add('aze');
        $storage->burn('aze');
        $this->assertEquals(0, $storage->count());
    }

    public function testHas()
    {
        $storage = new TokenStorage();
        $storage->add('aze');
        $this->assertTrue($storage->has('aze'));
        $this->assertFalse($storage->has('qsd'));
    }
}
