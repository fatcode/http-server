<?php declare(strict_types=1);

namespace FatCode\Tests\HttpServer\Server;

use FatCode\HttpServer\Exception\ServerHttpServerException;
use FatCode\HttpServer\Server\CallableMiddleware;
use FatCode\HttpServer\Server\NotFoundMiddleware;
use PHPUnit\Framework\TestCase;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class NotFoundMiddlewareTest extends TestCase
{
    public function testCanInstantiate() : void
    {
        self::assertInstanceOf(NotFoundMiddleware::class, new NotFoundMiddleware());
    }

    public function testProcess() : void
    {
        $expectedResponse = Mockery::mock(ResponseInterface::class);
        $middleware = new NotFoundMiddleware($expectedResponse);
        self::assertSame($expectedResponse, $middleware->process(
            Mockery::mock(ServerRequestInterface::class),
            Mockery::mock(RequestHandlerInterface::class)
        ));
    }
}
