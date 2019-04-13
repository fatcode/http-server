<?php declare(strict_types=1);

namespace FatCode\HttpServer;

use Psr\Http\Message\ServerRequestInterface;

interface ServerRequestFactory
{
    public function createServerRequest($input = null) : ServerRequestInterface;
}
