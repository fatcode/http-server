<?php declare(strict_types=1);

namespace FatCode\Http\Server;

use FastRoute\DataGenerator\GroupCountBased as GroupCountBasedDataGenerator;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use FastRoute\RouteParser\Std as StandardRouteParser;
use FastRoute\RouteCollector;
use FatCode\Http\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router implements MiddlewareInterface
{
    private $routes;

    public function __construct()
    {
        $this->routes = new RouteCollector(new StandardRouteParser(), new GroupCountBasedDataGenerator());
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $dispatcher = new GroupCountBasedDispatcher($this->routes->getData());
        $info = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($info[0]) {
            case Dispatcher::FOUND:
                $handler = $info[1];
                foreach ($info[2] as $name => $value) {
                    $request = $request->withAttribute($name, $value);
                }
                $response = $handler($request);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
            case Dispatcher::NOT_FOUND:
            default:
                return $handler->handle($request);
        }

        if (!$response instanceof ResponseInterface) {
            throw new ServerException(sprintf(
                'Route handler must return instance of `%s`, `%s` returned instead.',
                ResponseInterface::class,
                is_object($response) ? get_class($response) : gettype($response)
            ));
        }

        return $response;
    }

    public function put(string $route, callable $handler) : void
    {
        $this->routes->addRoute('PUT', $route, $handler);
    }

    public function delete(string $route, callable $handler) : void
    {
        $this->routes->addRoute('DELETE', $route, $handler);
    }

    public function patch(string $route, callable $handler) : void
    {
        $this->routes->addRoute('PATCH', $route, $handler);
    }

    public function options(string $route, callable $handler) : void
    {
        $this->routes->addRoute('OPTIONS', $route, $handler);
    }

    public function head(string $route, callable $handler) : void
    {
        $this->routes->addRoute('HEAD', $route, $handler);
    }

    public function get(string $route, callable $handler) : void
    {
        $this->routes->addRoute('GET', $route, $handler);
    }

    public function post(string $route, callable $handler) : void
    {
        $this->routes->addRoute('POST', $route, $handler);
    }
}
