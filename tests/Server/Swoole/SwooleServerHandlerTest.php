<?php declare(strict_types=1);

namespace FatCode\Tests\HttpServer\Server\Swoole;

use FatCode\HttpServer\Response;
use FatCode\HttpServer\Server\CallableMiddleware;
use FatCode\HttpServer\Server\HttpServerSettings;
use FatCode\HttpServer\Server\MiddlewarePipeline;
use FatCode\HttpServer\Server\Swoole\SwooleServerHandler;
use FatCode\HttpServer\ServerRequest;
use FatCode\HttpServer\ServerRequestFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SplQueue;
use stdClass;

final class SwooleServerHandlerTest extends TestCase
{
    public function testCanInstantiate() : void
    {
        self::assertInstanceOf(SwooleServerHandler::class, new SwooleServerHandler());
    }

    public function testOnRequest() : void
    {
        $psrResponse = new Response('test123');
        $psrResponse = $psrResponse->withHeader('test-header', 'test');
        $psrServerRequest = new ServerRequest();
        $psrServerRequest = $psrServerRequest->withHeader('Accept-Encoding', 'deflate, gzip;q=1.0, *;q=0.5');

        $serverRequest = Mockery::mock(stdClass::class);
        $serverResponse = Mockery::mock(stdClass::class);
        $serverResponse->shouldReceive('status')
            ->with($psrResponse->getStatusCode());
        $serverResponse->shouldReceive('header');
        $serverResponse->shouldReceive('end')
            ->with(gzdeflate('test123', 0));

        $requestFactory = Mockery::mock(ServerRequestFactory::class);
        $requestFactory
            ->shouldReceive('createServerRequest')
            ->with($serverRequest)
            ->andReturn($psrServerRequest);

        $server = new SwooleServerHandler($requestFactory);
        $server->setPipeline($this->mockMiddlewarePipeline($psrResponse));
        $server->setSettings(new HttpServerSettings());

        self::assertNull($server->onRequest($serverRequest, $serverResponse));
    }

    private function mockMiddlewarePipeline(ResponseInterface $expectedResponse) : MiddlewarePipeline
    {
        $queue = new SplQueue();
        $queue->enqueue(new CallableMiddleware(function () use ($expectedResponse) {
            return $expectedResponse;
        }));

        return new MiddlewarePipeline($queue);
    }
}
