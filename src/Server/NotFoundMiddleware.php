<?php declare(strict_types=1);

namespace FatCode\HttpServer\Server;

use FatCode\HttpServer\HttpStatusCode;
use FatCode\HttpServer\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class NotFoundMiddleware implements MiddlewareInterface
{
    private $response;

    public function __construct(ResponseInterface $response = null)
    {
        $this->response = $response ?? new Response('Not Found', HttpStatusCode::NOT_FOUND());
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface
    {
        return $this->response;
    }
}
