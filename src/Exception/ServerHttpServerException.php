<?php declare(strict_types=1);

namespace FatCode\HttpServer\Exception;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;

class ServerHttpServerException extends RuntimeException implements HttpServerException
{
    public static function forInvalidResponseFromCallableMiddleware($response) : self
    {
        return new self(sprintf(
            'Callable middleware must return instance of `%s`, but `%s` returned instead',
            ResponseInterface::class,
            is_object($response) ? 'instance of ' . get_class($response) : gettype($response)
        ));
    }

    public static function forInvalidMiddleware($middleware) : self
    {
        return new self(sprintf(
            'Expected middleware to be callable or instance of `%s`, but `%s` was given',
            MiddlewareInterface::class,
            is_object($middleware) ? 'instance of ' . get_class($middleware) : gettype($middleware)
        ));
    }
}
