<?php declare(strict_types=1);

namespace FatCode\Http\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

abstract class HttpException extends RuntimeException implements Exception
{
    abstract public function toResponse() : ResponseInterface;
}
