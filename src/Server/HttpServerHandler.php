<?php declare(strict_types=1);

namespace FatCode\Http\Server;

interface HttpServerHandler
{
    public function start(HttpServerSettings $settings, MiddlewarePipeline $pipeline) : void;
}
