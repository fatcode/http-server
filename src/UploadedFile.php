<?php declare(strict_types=1);

namespace FatCode\HttpServer;

use FatCode\HttpServer\Exception\UploadedFileException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

use function is_string;
use function is_writable;
use function move_uploaded_file;

class UploadedFile implements UploadedFileInterface
{
    private $file;
    private $size;
    private $status;
    private $movedTo;

    public function __construct(string $fileName, int $size, UploadStatus $status = null)
    {
        $this->file = $fileName;
        $this->size = $size;
        $this->status = $status ?? UploadStatus::SUCCESS();
    }

    public function getStatus() : UploadStatus
    {
        return $this->status;
    }

    public function getStream() : StreamInterface
    {
        if ($this->status !== UploadStatus::SUCCESS()) {
            throw UploadedFileException::forUploadedFileFailure($this);
        }

        return Stream::fromFile($this->file);
    }

    public function moveTo($targetPath) : void
    {
        if ($this->status !== UploadStatus::SUCCESS()) {
            throw UploadedFileException::forUploadedFileFailure($this);
        }

        if (null !== $this->movedTo) {
            throw UploadedFileException::forMoveAlreadyMovedFile($this);
        }

        if (!is_string($targetPath) || !is_writable($targetPath)) {
            throw UploadedFileException::forMoveToInvalidTargetPath($this, $targetPath);
        }

        if (false === move_uploaded_file($this->file, $targetPath)) {
            throw UploadedFileException::forMoveUploadedFileFailure($this, $targetPath);
        }

        $this->movedTo = $targetPath;
    }

    public function getSize() : int
    {
        return $this->size;
    }

    public function getError() : int
    {
        return $this->status->getValue();
    }

    public function getClientFilename()
    {
        throw UploadedFileException::forNotSupported(__METHOD__);
    }

    public function getClientMediaType()
    {
        throw UploadedFileException::forNotSupported(__METHOD__);
    }

    public function __toString() : string
    {
        return $this->file;
    }
}
