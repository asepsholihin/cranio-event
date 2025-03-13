<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ErrorMessageException extends Exception implements HttpExceptionInterface
{
    public function getStatusCode(): int
    {
        return 422;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
