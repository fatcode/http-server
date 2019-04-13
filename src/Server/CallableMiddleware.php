<?php declare(strict_types=1);

namespace FatCode\HttpServer\Server;

use FatCode\HttpServer\Exception\ServerHttpServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CallableMiddleware implements MiddlewareInterface
{
    private $middleware;

    public function __construct(callable $callable)
    {
        $this->middleware = $callable;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $response = ($this->middleware)($request, $handler);
        if (!$response instanceof ResponseInterface) {
            throw ServerHttpServerException::forInvalidResponseFromCallableMiddleware($this->middleware);
        }

        return $response;
    }
}
