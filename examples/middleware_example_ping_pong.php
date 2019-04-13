<?php declare(strict_types=1);
require_once '../vendor/autoload.php';

use FatCode\HttpServer\HttpServer;
use FatCode\HttpServer\Server\HttpServerSettings;
use Psr\Http\Message\ServerRequestInterface;
use FatCode\HttpServer\Response;

// Simple pong server.
$server = new HttpServer(new HttpServerSettings('0.0.0.0', 8080));
$server->use(function (ServerRequestInterface $request) : Response {
    if ($request->getUri()->getPath() === '/ping') {
        return new Response('Pong!');
    }
    return new Response('Please call /ping uri.');
});
$server->start();
