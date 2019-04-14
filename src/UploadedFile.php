<?php declare(strict_types=1);

namespace FatCode\HttpServer;

use Zend\Diactoros\UploadedFile as DiactorosUploadedFile;

class UploadedFile extends DiactorosUploadedFile
{
    public function __construct(
        $streamOrFile,
        int $size,
        UploadStatus $status,
        string $clientFilename = null,
        string $clientMediaType = null
    ) {
        parent::__construct($streamOrFile, $size, $status->getValue(), $clientFilename, $clientMediaType);
    }
}
