<?php declare(strict_types=1);

namespace FatCode\HttpServer\Exception;

use FatCode\HttpServer\UploadedFile;
use FatCode\HttpServer\UploadStatus;
use RuntimeException;

final class UploadedFileException extends RuntimeException implements HttpServerException
{
    private const UPLOAD_STATUS_MESSAGES = [
        UploadStatus::EXCEEDS_MAXIMUM_FORM_SIZE => ' uploaded file exceeded maximum declared form size',
        UploadStatus::EXCEEDS_MAXIMUM_INI_FILESIZE => ' uploaded file exceeded maximum allowed size',
        UploadStatus::FAILED => ' general failure, please check if file exists or tmp directory is writable.'
    ];

    public static function forUploadedFileFailure(UploadedFile $file) : self
    {
        return new self(
            'Uploaded file failed during upload process- ' .
            self::UPLOAD_STATUS_MESSAGES[$file->getStatus()->getValue()]
        );
    }

    public static function forMoveAlreadyMovedFile(UploadedFile $file) : self
    {
        return new self("Cannot move uploaded file - file `{$file}` is already moved");
    }

    public static function forMoveToInvalidTargetPath(UploadedFile $file, string $targetPath) : self
    {
        return new self(
            "Cannot move uploaded file `{$file}` to target path `{$targetPath}` - must be valid writable path."
        );
    }

    public static function forMoveUploadedFileFailure(UploadedFile $file, string $targetPath) : self
    {
        return new self("Failed during moving uploaded file `{$file}` to target path `{$targetPath}`.");
    }

    public static function forNotSupported(string $method) : self
    {
        return new self("Method `{$method}`` is not supported.");
    }
}
