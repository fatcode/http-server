<?php declare(strict_types=1);

namespace FatCode\Tests\Http;

use FatCode\Http\HttpServer;
use FatCode\Http\Server\HttpServerHandler;
use FatCode\Http\Server\HttpServerSettings;
use FatCode\Http\Server\MiddlewarePipeline;
use PHPUnit\Framework\TestCase;

class HttpServerTest extends TestCase
{
    public function testCanInstantiateWithoutArguments() : void
    {
        $server = new HttpServer();
        self::assertInstanceOf(HttpServer::class, $server);
    }

    public function testUse() : void
    {
        $handlerMock = $this->getHandlerMock();
        $middlewareMock = function() {};
        $server = new HttpServer(new HttpServerSettings(), $handlerMock);
        $server->use($middlewareMock);
        $server->start();

        $pipeline = $handlerMock->pipeline;
        self::assertCount(3, $pipeline);
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
