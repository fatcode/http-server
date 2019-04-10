<?php declare(strict_types=1);

namespace FatCode\Tests\Http\Server;

use FatCode\Http\Exception\ServerException;
use FatCode\Http\HttpMethod;
use FatCode\Http\Server\Router;
use FatCode\Http\ServerRequest;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouterTest extends TestCase
{
    public function testCanInstantiate() : void
    {
        self::assertInstanceOf(Router::class, new Router());
    }

    /**
     * @param string $route
     * @param string $uri
     * @param HttpMethod $method
     * @dataProvider provideValidMatchingRoutes
     */
    public function testProcess(string $route, string $uri, HttpMethod $method) : void
    {
        $router = new Router();
        $expectedResponse = Mockery::mock(ResponseInterface::class);
        $router->{strtolower($method->getValue())}($route, function () use ($expectedResponse) {
            return $expectedResponse;
        });

        self::assertSame($expectedResponse, $router->process(
            new ServerRequest($uri, $method),
            Mockery::mock(RequestHandlerInterface::class)
        ));
    }

    public function provideValidMatchingRoutes() : array
    {
        return [
            ['/test', '/test', HttpMethod::GET()],
            ['/test', '/test', HttpMethod::POST()],
            ['/test', '/test', HttpMethod::PATCH()],
            ['/test', '/test', HttpMethod::PUT()],
            ['/test', '/test', HttpMethod::DELETE()],
            ['/test', '/test', HttpMethod::OPTIONS()],
            ['/test', '/test', HttpMethod::HEAD()],
            ['/test/{a}', '/test/1', HttpMethod::GET()],
            ['/test/{a}', '/test/a', HttpMethod::GET()],
            ['/test/{a}', '/test/.', HttpMethod::GET()],
        ];
    }

    public function testProcessRouteWithAttribute() : void
    {
        $router = new Router();
        $expectedResponse = Mockery::mock(ResponseInterface::class);
        $router->post('/test/{attribute}', function (ServerRequestInterface $request) use ($expectedResponse) {
            self::assertSame('test', $request->getAttribute('attribute'));
            return $expectedResponse;
        });
        self::assertSame($expectedResponse, $router->process(
            new ServerRequest('/test/test', HttpMethod::POST()),
            Mockery::mock(RequestHandlerInterface::class)
        ));
    }

    public function testFailOnInvalidResponse() : void
    {
        $this->expectException(ServerException::class);
        $router = new Router();
        $router->patch('/test', function () {});
        $router->process(
            new ServerRequest('/test', HttpMethod::PATCH()),
            Mockery::mock(RequestHandlerInterface::class)
        );
    }

    public function testNotFound() : void
    {
        $router = new Router();
        $response = Mockery::mock(ResponseInterface::class);
        $requestHandler = Mockery::mock(RequestHandlerInterface::class);
        $requestHandler->shouldReceive('handle')->andReturn($response);
        $router->delete('/test', function () {});
        self::assertSame($response, $router->process(
            new ServerRequest('/test2', HttpMethod::GET()),
            $requestHandler
        ));
    }

    public function testMethodNotAllowed() : void
    {
        $router = new Router();
        $response = Mockery::mock(ResponseInterface::class);
        $requestHandler = Mockery::mock(RequestHandlerInterface::class);
        $requestHandler->shouldReceive('handle')->andReturn($response);
        $router->put('/test', function () {});
        self::assertSame($response, $router->process(
            new ServerRequest('/test', HttpMethod::GET()),
            $requestHandler
        ));
    }
}
