<?php declare(strict_types=1);

namespace FatCode\Tests\Http\Server;

use FatCode\Http\Server\CallableMiddleware;
use FatCode\Http\Server\MiddlewarePipeline;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SplQueue;

final class MiddlewarePipelineTest extends TestCase
{
    public function testCanInstantiate() : void
    {
        self::assertInstanceOf(MiddlewarePipeline::class, new MiddlewarePipeline(new SplQueue()));
    }

    public function testInvoke() : void
    {
        $queue = new SplQueue();
        $expectedResponse = Mockery::mock(ResponseInterface::class);
        $queue->enqueue(new CallableMiddleware(function () use ($expectedResponse) {
            return $expectedResponse;
        }));

        $pipeline = new MiddlewarePipeline($queue);
        $response = $pipeline(Mockery::mock(ServerRequestInterface::class));
        self::assertSame($expectedResponse, $response);
    }

    public function testProcess() : void
    {
        $expectedResponse = Mockery::mock(ResponseInterface::class);
        $queue = new SplQueue();
        $queue->enqueue(new CallableMiddleware(function (ServerRequestInterface $request, callable $next) {
            return $next($request);
        }));
        $queue->enqueue(new CallableMiddleware(function () use ($expectedResponse) {
            return $expectedResponse;
        }));

        $pipeline = new MiddlewarePipeline($queue);
        $response = $pipeline->process(Mockery::mock(ServerRequestInterface::class), $pipeline);
        self::assertSame($expectedResponse, $response);
    }
}
