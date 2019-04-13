<?php declare(strict_types=1);

namespace FatCode\HttpServer\Exception;

use InvalidArgumentException;

class SettingsException extends InvalidArgumentException implements HttpServerException
{
    public static function forInvalidPidFile(string $pid) : self
    {
        return new self("PID file `{$pid}` must be writable.");
    }
}
