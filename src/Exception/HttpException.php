<?php declare(strict_types=1);

namespace FatCode\HttpServer\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

abstract class HttpException extends RuntimeException implements HttpServerException
{
    abstract public function toResponse() : ResponseInterface;
}
