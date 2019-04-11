<?php declare(strict_types=1);

namespace FatCode\Tests\Http\Server\Swoole;

use FatCode\Http\Server\Swoole\SwooleServerRequestFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

final class SwooleServerRequestFactoryTest extends TestCase
{
    public function testCanInstantiate() : void
    {
        self::assertInstanceOf(SwooleServerRequestFactory::class, new SwooleServerRequestFactory());
    }

    public function testCreateServerRequest() : void
    {
        $swooleRequest = new class() {
            public $server = [
                'request_uri' => '/uri',
                'request_method' => 'post',
            ];
            public $header = [
                'test-header' => 'test-value',
            ];
            public $cookie = [
                'test-cookie' => 'test-value',
            ];
            public $get = [
                'test-get' => 'test-value',
            ];
        };
        $serverRequestFactory = new SwooleServerRequestFactory();
        $psrRequest = $serverRequestFactory->createServerRequest($swooleRequest);
        self::assertSame('/uri', $psrRequest->getUri()->getPath());
        self::assertSame('POST', $psrRequest->getMethod());
        self::assertSame(['test-value'], $psrRequest->getHeader('test-header'));
        self::assertSame(['test-cookie' => 'test-value'], $psrRequest->getCookieParams());
        self::assertSame(['test-get' => 'test-value'], $psrRequest->getQueryParams());
    }

    public function testInvalidHttpMethod() : void
    {
        $swooleRequest = new class() {
            public $server = [
                'request_method' => 'invalid',
            ];
        };
        $serverRequestFactory = new SwooleServerRequestFactory();
        $psrRequest = $serverRequestFactory->createServerRequest($swooleRequest);
        self::assertSame('GET', $psrRequest->getMethod());
    }
}
