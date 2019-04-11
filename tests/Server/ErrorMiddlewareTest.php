<?php declare(strict_types=1);

namespace FatCode\Tests\Http\Server;

use Exception;
use FatCode\Http\Exception\HttpException;
use FatCode\Http\Server\ErrorMiddleware;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ErrorMiddlewareTest extends TestCase
{
    public function testCanInstantiate() : void
    {
        self::assertInstanceOf(ErrorMiddleware::class, new ErrorMiddleware());
        self::assertInstanceOf(ErrorMiddleware::class, new ErrorMiddleware(function () {}));
    }

    public function testProcess() : void
    {
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $middleware = new ErrorMiddleware();
        $response = $middleware->process(
            Mockery::mock(ServerRequestInterface::class),
            $handler
        );

        self::assertSame(500, $response->getStatusCode());
    }

    public function testProcessWithErrorHandler() : void
    {
        $expectedResponse = Mockery::mock(ResponseInterface::class);
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $middleware = new ErrorMiddleware(function ($error) use ($expectedResponse) {
            self::assertInstanceOf(Exception::class, $error);
            return new class($expectedResponse) extends HttpException {
                private $expectedResponse;

                public function __construct($expectedResponse)
                {
                    $this->expectedResponse = $expectedResponse;
                    parent::__construct();
                }

                public function toResponse() : ResponseInterface
                {
                    return $this->expectedResponse;
                }
            };
        });
        $response = $middleware->process(
            Mockery::mock(ServerRequestInterface::class),
            $handler
        );

        self::assertSame($expectedResponse, $response);
    }
}
