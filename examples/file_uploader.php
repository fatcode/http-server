<?php declare(strict_types=1);
require_once '../vendor/autoload.php';

use FatCode\HttpServer\HttpServer;
use FatCode\HttpServer\HttpStatusCode;
use FatCode\HttpServer\Server\HttpServerSettings;
use Psr\Http\Message\ServerRequestInterface;
use FatCode\HttpServer\Response;

$settings = new HttpServerSettings('0.0.0.0', 8080);
$settings->setUploadDir(__DIR__ . '/uploads');
$settings->enableDebug();
// Uploader
$server = new HttpServer($settings);
$server->use(function (ServerRequestInterface $request) : Response {
    if ($request->getUri()->getPath() === '/upload') {
        $files = $request->getUploadedFiles();
        var_dump($files);
        return new Response('Pong!');
    }
    return new Response('Please call POST /upload uri.', HttpStatusCode::NOT_FOUND());
});
$server->start();
