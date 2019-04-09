<?php declare(strict_types=1);

namespace FatCode\Http\Exception;

use RuntimeException;

class ResponseException extends RuntimeException implements Exception
{
    public static function forWritingToCompleteResponse() : self
    {
        return new self('Cannot write to the response, response is already completed.');
    }
}
