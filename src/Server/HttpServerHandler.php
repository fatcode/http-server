<?php declare(strict_types=1);

namespace FatCode\HttpServer\Server;

interface HttpServerHandler
{
    public function start(HttpServerSettings $settings, MiddlewarePipeline $pipeline) : void;
}
