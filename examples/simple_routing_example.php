<?php
require_once '../vendor/autoload.php';

use FatCode\Http\HttpServer;
use FatCode\Http\HttpStatusCode;
use FatCode\Http\Response;
use FatCode\Http\Server\HttpServerSettings;
use FatCode\Http\Server\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$router = new Router();
$router->get('/hello/{name}', function (ServerRequestInterface $request) : ResponseInterface {
    return new Response(sprintf('Hello %s!', $request->getAttribute('name')));
});

$router->post('/hello/{name}', function (ServerRequestInterface $request) : ResponseInterface {
    return new Response(sprintf('New %s!', $request->getAttribute('name')), HttpStatusCode::CREATED());
});

$server = new HttpServer(new HttpServerSettings('0.0.0.0', 8080));
$server->use($router);
$server->start();
