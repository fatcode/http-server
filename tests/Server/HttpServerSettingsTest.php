<?php declare(strict_types=1);

namespace FatCode\Tests\HttpServer\Server;

use FatCode\HttpServer\Exception\SettingsException;
use FatCode\HttpServer\Server\DispatchMode;
use FatCode\HttpServer\Server\HttpServerSettings;
use PHPUnit\Framework\TestCase;

final class HttpServerSettingsTest extends TestCase
{
    public function testCanInstantiate() : void
    {
        self::assertInstanceOf(HttpServerSettings::class, new HttpServerSettings());
    }

    public function testToArray() : void
    {
        $settings = new HttpServerSettings();
        $settings->setBufferOutputSize(1);
        $settings->setDispatchMode(DispatchMode::FIXED());
        $settings->setMaxConnections(2);
        $settings->setMaxRequests(3);
        $settings->setPidFile(__FILE__);
        $settings->setPort(20);
        $settings->setResponseCompression(4);
        $settings->setWorkers(4);
        $settings->setUploadDir(__DIR__);

        self::assertSame(
            [
                'address' => '0.0.0.0',
                'port' => 20,
                'workers' => 4,
                'max_requests' => 3,
                'max_connections' => 2,
                'output_dir' => __DIR__,
                'buffer_output_size' => 1,
                'response_compression_level' => 4,
                'dispatch_mode' => 2,
                'pid_file' => __FILE__,
            ],
            $settings->toArray()
        );
    }

    public function testInvalidPidFile() : void
    {
        $this->expectException(SettingsException::class);
        $settings = new HttpServerSettings();
        $settings->setPidFile('/notwritable');
    }
}
