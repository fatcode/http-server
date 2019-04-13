<?php declare(strict_types=1);

namespace FatCode\Tests\HttpServer;

use FatCode\HttpServer\ServerRequest;
use FatCode\HttpServer\UploadStatus;
use PHPUnit\Framework\TestCase;
use FatCode\HttpServer\UploadedFile;

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
}
