<?php declare(strict_types=1);

namespace FatCode\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

/**
 * Iterates a queue of middleware and executes them.
 */
final class MiddlewareCursor implements RequestHandlerInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private $parent;

    /**
     * @var SplQueue
     */
    private $queue;

    public function __construct(SplQueue $queue, RequestHandlerInterface $parent)
    {
        $this->queue = clone $queue;
        $this->parent = $parent;
    }

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        return $this->handle($request);
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        // Bubble up
        if ($this->queue->isEmpty()) {
            return $this->parent->handle($request);
        }

        /** @var MiddlewareInterface $middleware */
        $middleware = $this->queue->dequeue();

        return $middleware->process($request, $this);
    }
}
