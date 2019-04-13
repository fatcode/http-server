<?php declare(strict_types=1);

namespace FatCode\Tests\HttpServer;

use FatCode\HttpServer\Exception\ServerHttpServerException;
use FatCode\HttpServer\HttpServer;
use FatCode\HttpServer\Server\ErrorMiddleware;
use FatCode\HttpServer\Server\HttpServerHandler;
use FatCode\HttpServer\Server\HttpServerSettings;
use FatCode\HttpServer\Server\MiddlewarePipeline;
use FatCode\HttpServer\Server\NotFoundMiddleware;
use PHPUnit\Framework\TestCase;

final class HttpServerTest extends TestCase
{
    public function testCanInstantiateWithoutArguments() : void
    {
        $server = new HttpServer();
        self::assertInstanceOf(HttpServer::class, $server);
    }

    public function testComposeMiddlewarePipe() : void
    {
        $handlerMock = $this->getHandlerMock();
        $server = new HttpServer(new HttpServerSettings(), $handlerMock);
        $server->start();
        self::assertCount(2, $handlerMock->pipeline);
        self::assertInstanceOf(ErrorMiddleware::class, $handlerMock->pipeline->dequeue());
        self::assertInstanceOf(NotFoundMiddleware::class, $handlerMock->pipeline->dequeue());
    }

    public function testUseSuccess() : void
    {
        $handlerMock = $this->getHandlerMock();
        $middlewareMock = function () {
        };
        $server = new HttpServer(new HttpServerSettings(), $handlerMock);
        $server->use($middlewareMock);
        $server->start();

        self::assertCount(3, $handlerMock->pipeline);
    }

    public function testUseFail() : void
    {
        $this->expectException(ServerHttpServerException::class);
        $server = new HttpServer();
        $server->use(false);
    }

    public function testOnStart() : void
    {
        $onStartExecuted = false;
        $onStart = function () use (&$onStartExecuted) {
            $onStartExecuted = true;
        };
        $server = new HttpServer(null, $this->getHandlerMock());
        $server->onStart($onStart);

        self::assertFalse($onStartExecuted);
        $server->start();
        self::assertTrue($onStartExecuted);
    }

    public function testOnStop() : void
    {
        $onStopExecuted = false;
        $onStop = function () use (&$onStopExecuted) {
            $onStopExecuted = true;
        };
        $server = new HttpServer(null, $this->getHandlerMock());
        $server->onStop($onStop);

        self::assertFalse($onStopExecuted);
        $server->start();
        self::assertTrue($onStopExecuted);
    }

    private function getHandlerMock() : HttpServerHandler
    {
        return new class implements HttpServerHandler {
            public $pipeline;
            public $settings;
            public function start(HttpServerSettings $settings, MiddlewarePipeline $pipeline) : void
            {
                $this->pipeline = $pipeline;
                $this->settings = $settings;
            }
        };
    }
}
