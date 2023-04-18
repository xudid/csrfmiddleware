<?php

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Xudid\CsrfMiddleware\CsrfMiddleware;

class CsrfMiddlewareTest extends TestCase
{
    public function testMiddlewareImplementsPsrMiddlewareInterface()
    {
        $middleware = new CsrfMiddleware();
        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }
}
