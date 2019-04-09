<?php declare(strict_types=1);

namespace FatCode\Tests\Http;

use FatCode\Http\Request;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Zend\Diactoros\Uri;

final class RequestTest extends TestCase
{
    /**
     * @dataProvider provideValidURIs
     * @param $uri
     */
    public function testCanInstantiate($uri) : void
    {
        $request = new Request($uri);
        self::assertInstanceOf(Request::class, $request);
    }

    /**
     * @dataProvider provideInvalidURIs
     * @param $uri
     */
    public function testCantInstantiate($uri) : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Request($uri);
    }

    public function provideValidURIs() : array
    {
        return [
            ['valid/uri'],
            [new Uri('another/uri')]
        ];
    }

    public function provideInvalidURIs() : array
    {
        return [
            [false],
            [12],
            [new \stdClass()],
        ];
    }
}
