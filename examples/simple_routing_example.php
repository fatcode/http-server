<?php
require_once '../vendor/autoload.php';

use FatCode\HttpServer\HttpServer;
use FatCode\HttpServer\HttpStatusCode;
use FatCode\HttpServer\Response;
use FatCode\HttpServer\Server\HttpServerSettings;
use FatCode\HttpServer\Server\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$router = new Router();
$router->get('/hello/{name}', function (ServerRequestInterface $request) : ResponseInterface {
    return new Response("Hello {$request->getAttribute('name')}!");
});

$router->post('/hello/{name}', function (ServerRequestInterface $request) : ResponseInterface {
    return new Response("New {$request->getAttribute('name')}!", HttpStatusCode::CREATED());
});

$server = new HttpServer(new HttpServerSettings('0.0.0.0', 8080));
$server->use($router);
$server->start();
