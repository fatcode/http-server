<?php declare(strict_types=1);

namespace FatCode\Tests\Http;

use FatCode\Http\Exception\ResponseException;
use FatCode\Http\HttpStatusCode;
use FatCode\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testCanInstantiate() : void
    {
       $response = new Response('Test body', HttpStatusCode::CREATED());

       self::assertSame(HttpStatusCode::CREATED, $response->getStatusCode());
       self::assertSame('Test body', (string) $response->getBody());
    }

    public function testWrite() : void
    {
        $response = new Response('Test body', HttpStatusCode::CREATED());
        $response->write('123');
        self::assertSame('123t body', (string) $response->getBody());
    }

    public function testEnd() : void
    {
        $this->expectException(ResponseException::class);
        $response = new Response('Test body', HttpStatusCode::CREATED());
        $response->end();
        self::assertTrue($response->isComplete());
        $response->write('123');
    }

    public function testWithStatus() : void
    {
        $response = new Response('Test body', HttpStatusCode::CREATED());
        $withStatus = $response->withStatus(HttpStatusCode::NOT_FOUND);
        self::assertNotSame($response, $withStatus);
        self::assertSame(HttpStatusCode::CREATED, $response->getStatusCode());
        self::assertSame(HttpStatusCode::NOT_FOUND, $withStatus->getStatusCode());
    }
}
