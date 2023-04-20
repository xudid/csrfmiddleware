<?php

namespace Xudid\CsrfMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    private TokenStorageInterface $tokensStorage;
    private string $requestKey;

    public function __construct(TokenStorageInterface $tokensStorage, $requestKey = 'csrf_token')
    {
        $this->tokensStorage = $tokensStorage;
        $this->requestKey = $requestKey;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($request->getMethod() == 'GET') {
            return $response;
        }

        if ($request->getMethod() == 'POST') {
            $body = $request->getParsedBody() ?? [];

            if (!$this->requestBodyHasToken($body)) {
                throw new CsrfException();
            }
            $requestToken = $this->requestBodyGetToken($body);
            if (!$this->tokensStorage->has($requestToken)) {
                throw new CsrfException();
            }
            $this->tokensStorage->burn($requestToken);
            return $response;
        }

        throw new CsrfException();
    }

    public function requestBodyHasToken($body)
    {
        return array_key_exists($this->requestKey, $body);
    }

    public function requestBodyGetToken($body)
    {
        return $body[$this->requestKey];
    }
}
