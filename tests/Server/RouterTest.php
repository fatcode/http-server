<?php declare(strict_types=1);

namespace FatCode\Tests\Http\Server;

use FatCode\Http\HttpMethod;
use FatCode\Http\Server\Router;
use FatCode\Http\ServerRequest;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouterTest extends TestCase
{
    public function testCanInstantiate() : void
    {
        self::assertInstanceOf(Router::class, new Router());
    }

    public function testProcessSimpleRoute() : void
    {
        $expectedResponse = Mockery::mock(ResponseInterface::class);
        $router = new Router();
        $router->get('/test', function () use ($expectedResponse) {
            return $expectedResponse;
        });

        $request = new ServerRequest('/test', HttpMethod::GET());
        self::assertSame($expectedResponse, $router->process(
            $request,
            Mockery::mock(RequestHandlerInterface::class)
        ));
    }
}
