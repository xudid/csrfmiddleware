<?php

namespace Xudid\CsrfMiddleware;
use Exception;
use Throwable;

class CsrfException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
