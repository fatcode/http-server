<?php declare(strict_types=1);

namespace FatCode\Http\Server\Swoole;

use FatCode\Http\Server\HttpServerHandler;
use FatCode\Http\Server\HttpServerSettings;
use FatCode\Http\Server\MiddlewarePipeline;
use FatCode\Http\ServerRequestFactory;
use Swoole\Http\Server;
use RuntimeException;
use Swoole\Runtime as SwooleRuntime;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;

use function extension_loaded;
use function method_exists;

class SwooleServerHandler implements HttpServerHandler
{
    /** @var MiddlewarePipeline */
    private $pipeline;
    /** @var HttpServerSettings */
    private $settings;
    private $server;
    private $requestFactory;

    public function __construct(ServerRequestFactory $requestFactory = null)
    {
        if (!extension_loaded('swoole')) {
            throw new RuntimeException('Swoole extenstion is missing, please install it and try again.');
        }

        $this->requestFactory = $requestFactory ?? new SwooleServerRequestFactory();
    }

    public function setSettings(HttpServerSettings $settings) : void
    {
        $this->settings = $settings;
    }

    public function setPipeline(MiddlewarePipeline $pipeline) : void
    {
        $this->pipeline = $pipeline;
    }

    public function start(HttpServerSettings $settings, MiddlewarePipeline $pipeline) : void
    {
        $this->setPipeline($pipeline);
        $this->setSettings($settings);

        // Support coroutine if possible.
        if (method_exists(SwooleRuntime::class, 'enableCoroutine')) {
            SwooleRuntime::enableCoroutine(true);
        }

        $this->server = new Server($settings->getAddress(), $settings->getPort(), SWOOLE_PROCESS, SWOOLE_TCP);
        $this->server->set($this->translateSettings($settings));
        $this->server->on('Request', [$this, 'onRequest']);
        $this->server->start();
    }

    /**
     * Handles client request by processing psr-15 middleware pipeline and serving response
     * from the pipeline.
     *
     * Note: Swoole objects cannot be instantiated in user-land so type-hinting against them makes
     * testing impossible.
     * @param SwooleHttpRequest $request
     * @param SwooleHttpResponse $response
     */
    public function onRequest($request, $response) : void
    {
        $psrRequest = $this->requestFactory->createServerRequest($request);
        $pipeline = clone $this->pipeline;
        $psrResponse = $pipeline->process($psrRequest, $pipeline);

        // Set headers
        foreach ($psrResponse->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $response->header($name, $value);
            }
        }

        // Response body.
        $body = $psrResponse->getBody()->getContents();

        // Status code
        $response->status($psrResponse->getStatusCode());

        // Protect server software header.
        $response->header('software-server', '');
        $response->header('server', '');

        // Support gzip/deflate encoding.
        if ($psrRequest->hasHeader('accept-encoding')) {
            $encoding = explode(
                ',',
                strtolower(implode(',', $psrRequest->getHeader('accept-encoding')))
            );
            if (in_array('gzip', $encoding, true)) {
                $response->header('content-encoding', 'gzip');
                $body = gzencode($body, $this->settings->getCompressionLevel());
            } elseif (in_array('deflate', $encoding, true)) {
                $response->header('content-encoding', 'deflate');
                $body = gzdeflate($body, $this->settings->getCompressionLevel());
            }
        }
        $response->end($body);
    }

    private function translateSettings(HttpServerSettings $settings) : array
    {
        return $settings->toArray();
    }
}
