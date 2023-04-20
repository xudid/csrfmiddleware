<?php

use Core\Security\Token;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Xudid\CsrfMiddleware\CsrfException;
use Xudid\CsrfMiddleware\CsrfMiddleware;
use Xudid\CsrfMiddleware\TokenStorage;

class CsrfMiddlewareTest extends TestCase
{
    public function testMiddlewareImplementsPsrMiddlewareInterface()
    {
        $storage = new TokenStorage();
        $middleware = new CsrfMiddleware($storage);
        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    public function testMiddlewareAcceptGetRequestRejectAllOther()
    {
        $storage = new TokenStorage();
        $middleware = new CsrfMiddleware($storage);
        $requestMockBuilder = $this->getMockBuilder(ServerRequestInterface::class);
        $request = $requestMockBuilder->getMock();
        $request->expects($this->once())->method('getMethod')->willReturn('GET');

        $handlerMockBuilder = $this->getMockBuilder(RequestHandlerInterface::class);
        $handler = $handlerMockBuilder->getMock();

        $response = $middleware->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testRejectPostRequestWithoutCsrfToken()
    {
        $storage = new TokenStorage();
        $middleware = new CsrfMiddleware($storage);

        $requestMockBuilder = $this->getMockBuilder(ServerRequestInterface::class);
        $request = $requestMockBuilder->getMock();
        $request->expects($this->atLeast(2))->method('getMethod')->willReturn('POST');

        $handlerMockBuilder = $this->getMockBuilder(RequestHandlerInterface::class);
        $handler = $handlerMockBuilder->getMock();

        $this->expectException(CsrfException::class);
        $middleware->process($request, $handler);
    }

    public function testPostRequestWithValidCsrfFieldAccepted()
    {
        $token = (string)new Token();
        $storage = new TokenStorage([$token]);
        $middleware = new CsrfMiddleware($storage);

        $requestMockBuilder = $this->getMockBuilder(ServerRequestInterface::class);
        $request = $requestMockBuilder->getMock();

        $request->method('getMethod')->will($this->returnValue('POST'));
        $request->method('getParsedBody')->will($this->returnValue(['csrf_token' => $token]));

        $handlerMockBuilder = $this->getMockBuilder(RequestHandlerInterface::class);
        $handler = $handlerMockBuilder->getMock();

        $response = $middleware->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testCanNotReplayAValidToken()
    {
        $token = (string)new Token();
        $storage = new TokenStorage([$token]);
        $middleware = new CsrfMiddleware($storage);

        $requestMockBuilder = $this->getMockBuilder(ServerRequestInterface::class);
        $request = $requestMockBuilder->getMock();

        $request->method('getMethod')->will($this->returnValue('POST'));
        $request->method('getParsedBody')->will($this->returnValue(['csrf_token' => $token]));

        $handlerMockBuilder = $this->getMockBuilder(RequestHandlerInterface::class);
        $handler = $handlerMockBuilder->getMock();

        $middleware->process($request, $handler);

        $this->expectException(CsrfException::class);
        $middleware->process($request, $handler);
    }
}
