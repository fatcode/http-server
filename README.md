# HttpServer [![Build Status](https://travis-ci.org/fatcode/http-server.svg?branch=master)](https://travis-ci.org/fatcode/http-server) [![Maintainability](https://api.codeclimate.com/v1/badges/007f06cac71f9139a9ff/maintainability)](https://codeclimate.com/github/fatcode/http-server/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/007f06cac71f9139a9ff/test_coverage)](https://codeclimate.com/github/fatcode/http-server/test_coverage)

## Requirements

 - `>= PHP 7.2`
 - `swoole extension`
 - `zlib extension`

## Installation

`composer install fatcode/http-server`

## Quick start

```php
<?php declare(strict_types=1);

use FatCode\HttpServer\HttpServer;
use FatCode\HttpServer\Server\Router;
use FatCode\HttpServer\Response;

// Instantiates router for registering resources:
$router = new Router();
$router->get('/hello', function () {
    return new Response('Hello You!');
});

// Run server at localhost:80
$server = new HttpServer();
$server->use($router);
$server->start();
```

The above example creates server that uses router with registered one resource. Server will listen
on `localhost` at port `8080`.

 > **Please Note:** Package is supporting PSR-7, that means all your registered handlers should expect 
 > `ServerRequestInterface` as an input, and return `ResponseInterface` as a result.

## Running server as a daemon

Http server provides flexible configuration class, depending on your settings server can be daemonized, run on specific
port, listen to specific amount of incoming connections and so on. 

More options can be found in the [class docblock itself](src/Server/HttpServerSettings.php).

```php
<?php declare(strict_types=1);

use FatCode\HttpServer\HttpServer;
use FatCode\HttpServer\Server\HttpServerSettings;

// Setting pid file will make server run as a daemon.
$settings = new HttpServerSettings('0.0.0.0', 8080);
$settings->setPidFile(sys_get_temp_dir() . '/my_pid.pid');

// Note this server will always respond with 404 response, as there is
// no router passed that can handle the request.
$server = new HttpServer();
$server->start();
```

## Middleware and PSR-15 support

Registering and using PSR-15 compatible middleware is trivial, just pass an instance of given middleware or closure itself
to `HttpServer::use` method in the right order. In fact `FatCode\HttpServer\Server\Router` class is PSR-15 middleware itself.

```php
<?php declare(strict_types=1);

use FatCode\HttpServer\HttpServer;
use FatCode\HttpServer\Response;
use Psr\Http\Message\ServerRequestInterface;

// Simple pong server.
$server = new HttpServer();
$server->use(function (ServerRequestInterface $request) : Response {
    if ($request->getUri()->getPath() === '/ping') {
        return new Response('Pong!');
    }
    return new Response('Please call /ping uri.');
});
$server->start();
```

## Request, Response and PSR-7

Http package provides convenient PSR-7 implementation based on `zendframework/zend-diactoros` package.
Shortly saying `FatCode\HttpServer\ServerRequest` and `FatCode\HttpServer\Response` objects utilize `zend-diactoros`
exceptions and interface with some additions. When working with these objects please keep it in mind.

### Creating new response object

```php
<?php declare(strict_types=1);

use FatCode\HttpServer\Response;
use FatCode\HttpServer\HttpStatusCode;

// Creates new response with status code 200.
$response = new Response("Hello world!", HttpStatusCode::OK());
```

For your convenience package declares `HttpStatusCode` enum that helps with generation valid http responses.
More status codes can be found in the [class itself.](src/HttpStatusCode.php).

### Working with request object

#### Reading route parameters
When declaring parametrized route you can access retrieve the value by calling `FatCode\HttpServer\ServerRequest::getAttribute` method.
Consider the following example:
```php
<?php declare(strict_types=1);

use FatCode\HttpServer\HttpServer;
use Psr\Http\Message\ServerRequestInterface;
use FatCode\HttpServer\Response;
use FatCode\HttpServer\Server\Router;

// Initialize router
$router = new Router();

// Register parametrized route
$router->get('/hello/{name}', function (ServerRequestInterface $request) : Response {
    // Return response
    return new Response("Hello, your name is {$request->getAttribute('name')}");
});

// Setup http server
$server = new HttpServer();
$server->use($router);
$server->start();
```

For more examples you can visit [examples directory](examples).
