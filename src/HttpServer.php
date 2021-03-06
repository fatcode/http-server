<?php declare(strict_types=1);

namespace FatCode\HttpServer;

use FatCode\HttpServer\Exception\ServerHttpServerException;
use FatCode\HttpServer\Server\CallableMiddleware;
use FatCode\HttpServer\Server\ErrorMiddleware;
use FatCode\HttpServer\Server\HttpServerHandler;
use FatCode\HttpServer\Server\HttpServerSettings;
use FatCode\HttpServer\Server\MiddlewarePipeline;
use FatCode\HttpServer\Server\NotFoundMiddleware;
use FatCode\HttpServer\Server\Swoole\SwooleServerHandler;
use Psr\Http\Server\MiddlewareInterface;
use SplQueue;

class HttpServer
{
    private $middleware;
    private $settings;
    private $handler;
    private $errorListener;
    private $startListener;
    private $stopListener;

    public function __construct(HttpServerSettings $settings = null, HttpServerHandler $handler = null)
    {
        $this->settings = $settings ?? new HttpServerSettings();
        $this->handler = $handler ?? new SwooleServerHandler();
        $this->middleware = [];
    }

    /**
     * @param callable|MiddlewareInterface $middleware
     * @example
     * $server->use(function(ServerRequestInterface $request, callable $next) : ResponseInterface {
     *     $next($request);
     *     return new Response('Hello!');
     * });
     */
    public function use($middleware) : void
    {
        if (!$middleware instanceof MiddlewareInterface) {
            if (!is_callable($middleware)) {
                throw ServerHttpServerException::forInvalidMiddleware($middleware);
            }
            $middleware = new CallableMiddleware($middleware);
        }

        $this->middleware[] = $middleware;
    }

    public function onError(callable $handler) : void
    {
        $this->errorListener = $handler;
    }

    public function onStart(callable $handler) : void
    {
        $this->startListener = $handler;
    }

    public function onStop(callable $handler) : void
    {
        $this->stopListener = $handler;
    }

    public function start() : void
    {
        if (isset($this->startListener)) {
            ($this->startListener)($this);
        }

        $this->handler->start(
            $this->settings,
            $this->buildMiddlewarePipeline()
        );

        if (isset($this->stopListener)) {
            ($this->stopListener)($this);
        }
    }

    private function buildMiddlewarePipeline() : MiddlewarePipeline
    {
        $pipeline = new SplQueue();
        $pipeline->enqueue(new ErrorMiddleware($this->errorListener));
        foreach ($this->middleware as $middleware) {
            $pipeline->enqueue($middleware);
        }
        $pipeline->enqueue(new  NotFoundMiddleware());
        return new MiddlewarePipeline($pipeline);
    }
}
