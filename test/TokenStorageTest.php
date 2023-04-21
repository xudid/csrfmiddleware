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

    public function testDefaultLimit()
    {
        $storage = new TokenStorage();
        $limit = 60;
        for ($i = 1; $i <= $limit; $i++) {
            $storage->add('aze' . $i);
        }
        $this->assertEquals(50, $storage->count());
        $this->assertFalse($storage->has('aze' . 10));
        $this->assertTrue($storage->has('aze' . 60));
    }

    public function testLimit()
    {
        $storage = new TokenStorage();
        $storage->limit(100);
        $limit = 160;
        for ($i = 1; $i <= $limit; $i++) {
            $storage->add('aze' . $i);
        }
        $this->assertEquals(100, $storage->count());
        $this->assertFalse($storage->has('aze' . 60));
        $this->assertTrue($storage->has('aze' . 160));
    }
}
