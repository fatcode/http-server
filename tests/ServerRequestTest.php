<?php declare(strict_types=1);

namespace FatCode\Tests\HttpServer;

use FatCode\HttpServer\ServerRequest;
use FatCode\HttpServer\UploadStatus;
use PHPUnit\Framework\TestCase;
use FatCode\HttpServer\UploadedFile;
use stdClass;

final class ServerRequestTest extends TestCase
{
    public function testCanInstantiate() : void
    {
        self::assertInstanceOf(ServerRequest::class, new ServerRequest());
    }

    public function testWithUploadedFiles() : void
    {
        $request = new ServerRequest();
        self::assertEmpty($request->getUploadedFiles());

        $updatedRequest = $request->withUploadedFiles([
            new UploadedFile('/tmp/1', 10, UploadStatus::SUCCESS()),
            new UploadedFile('/tmp/2', 10, UploadStatus::SUCCESS()),
        ]);

        self::assertNotEmpty($updatedRequest->getUploadedFiles());
    }

    public function testWithParsedBody() : void
    {
        $expectedBody = new stdClass();
        $request = new ServerRequest();
        $updatedRequest = $request->withParsedBody($expectedBody);
        self::assertNotSame($request, $updatedRequest);
        self::assertSame($expectedBody, $updatedRequest->getParsedBody());
    }

    public function testWithAttributes() : void
    {
        
    }
}
