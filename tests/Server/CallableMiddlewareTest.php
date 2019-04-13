<?php declare(strict_types=1);

namespace FatCode\Tests\HttpServer\Server;

use FatCode\HttpServer\Exception\ServerHttpServerException;
use FatCode\HttpServer\Server\CallableMiddleware;
use PHPUnit\Framework\TestCase;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CallableMiddlewareTest extends TestCase
{
    public function testCanInstantiate() : void
    {
        self::assertInstanceOf(CallableMiddleware::class, new CallableMiddleware(function () {}));
    }

    public function testProcess() : void
    {
        $expectedResponse = Mockery::mock(ResponseInterface::class);
        $middleware = new CallableMiddleware(function () use ($expectedResponse) {
            return $expectedResponse;
        });
        self::assertSame($expectedResponse, $middleware->process(
            Mockery::mock(ServerRequestInterface::class),
            Mockery::mock(RequestHandlerInterface::class)
        ));
    }

    public function testProcessInvalidMiddleware() : void
    {
        $this->expectException(ServerHttpServerException::class);
        $middleware = new CallableMiddleware(function () {});
        $middleware->process(
            Mockery::mock(ServerRequestInterface::class),
            Mockery::mock(RequestHandlerInterface::class)
        );
    }
}
