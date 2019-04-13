<?php declare(strict_types=1);

namespace FatCode\HttpServer;

use FatCode\Enum;

use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

/**
 * @method static UploadStatus SUCCESS
 * @method static UploadStatus EXCEEDS_MAXIMUM_INI_FILESIZE
 * @method static UploadStatus EXCEEDS_MAXIMUM_FORM_SIZE
 * @method static UploadStatus FAILED
 */
final class UploadStatus extends Enum
{
    public const SUCCESS = UPLOAD_ERR_OK;
    public const EXCEEDS_MAXIMUM_INI_FILESIZE = UPLOAD_ERR_INI_SIZE;
    public const EXCEEDS_MAXIMUM_FORM_SIZE = UPLOAD_ERR_FORM_SIZE;
    public const FAILED = UPLOAD_ERR_NO_FILE;
}
