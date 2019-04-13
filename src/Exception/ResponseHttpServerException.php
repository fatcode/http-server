<?php declare(strict_types=1);

namespace FatCode\HttpServer\Exception;

use RuntimeException;

class ResponseHttpServerException extends RuntimeException implements HttpServerException
{
    public static function forWritingToCompleteResponse() : self
    {
        return new self('Cannot write to the response, response is already completed.');
    }
}
