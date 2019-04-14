<?php declare(strict_types=1);

namespace FatCode\HttpServer;

use FatCode\Enum;

use const UPLOAD_ERR_CANT_WRITE;
use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_NO_TMP_DIR;
use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_PARTIAL;

/**
 * @method static UploadStatus SUCCESS
 * @method static UploadStatus EXCEEDS_MAXIMUM_INI_FILESIZE
 * @method static UploadStatus EXCEEDS_MAXIMUM_FORM_SIZE
 * @method static UploadStatus WRITE_FAILURE
 * @method static UploadStatus PARTIALLY_WRITTEN
 * @method static UploadStatus MISSING_TEMPORARY_DIRECTORY
 * @method static UploadStatus MISSING_FILE
 */
final class UploadStatus extends Enum
{
    public const SUCCESS = UPLOAD_ERR_OK;
    public const EXCEEDS_MAXIMUM_INI_FILESIZE = UPLOAD_ERR_INI_SIZE;
    public const EXCEEDS_MAXIMUM_FORM_SIZE = UPLOAD_ERR_FORM_SIZE;
    public const WRITE_FAILURE = UPLOAD_ERR_CANT_WRITE;
    public const PARTIALLY_WRITTEN = UPLOAD_ERR_PARTIAL;
    public const MISSING_TEMPORARY_DIRECTORY = UPLOAD_ERR_NO_TMP_DIR;
    public const MISSING_FILE = UPLOAD_ERR_NO_FILE;
}
