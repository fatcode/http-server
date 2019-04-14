<?php declare(strict_types=1);

namespace FatCode\HttpServer\Server\Swoole;

use FatCode\HttpServer\Server\HttpServerHandler;
use FatCode\HttpServer\Server\HttpServerSettings;
use FatCode\HttpServer\Server\MiddlewarePipeline;
use FatCode\HttpServer\ServerRequestFactory;
use RuntimeException;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\Http\Server;
use Swoole\Runtime as SwooleRuntime;
use function extension_loaded;
use function method_exists;

class SwooleServerHandler implements HttpServerHandler
{
    private const SETTINGS_MAP = [
        'buffer_output_size' => 'buffer_output_size',
        'workers' => 'worker_num',
        'max_requests' => 'max_requests',
        'max_connections' => 'max_conn',
        'upload_dir' => 'upload_tmp_dir',
        'dispatch_mode' => 'dispatch_mode',
        'pid_file' => 'pid_file',
        'debug' => 'debug_mode',
    ];
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
        $hash = $settings->toArray();

        $swooleSettings = [];
        foreach ($hash as $key => $value) {
            if (!isset(self::SETTINGS_MAP[$key])) {
                continue;
            }
            $swooleSettings[self::SETTINGS_MAP[$key]] = $hash[$key];
            if ($key === 'pid_file') {
                $swooleSettings['daemonize'] = 1;
            }
        }

        return $swooleSettings;
    }
}
