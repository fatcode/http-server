<?php declare(strict_types=1);

namespace FatCode\HttpServer\Server\Swoole;

use FatCode\Exception\EnumException;
use FatCode\HttpServer\HttpMethod;
use FatCode\HttpServer\ServerRequest;
use FatCode\HttpServer\ServerRequestFactory;
use FatCode\HttpServer\UploadedFile;
use FatCode\HttpServer\UploadStatus;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Swoole\Http\Request;
use Throwable;

use function Zend\Diactoros\marshalMethodFromSapi;
use function Zend\Diactoros\marshalUriFromSapi;
use function strtoupper;

use const CASE_UPPER;

class SwooleServerRequestFactory implements ServerRequestFactory
{
    /**
     * @param Request $input
     * @return ServerRequestInterface
     */
    public function createServerRequest($input = null) : ServerRequestInterface
    {
        try {
            $body = (string) $input->rawContent();
        } catch (Throwable $throwable) {
            $body = '';
        }

        // Normalize server params
        $serverParams = array_change_key_case($input->server, CASE_UPPER);
        $headers = $input->header ?? [];

        // Http method
        try {
            $httpMethod = HttpMethod::fromValue(strtoupper(marshalMethodFromSapi($serverParams)));
        } catch (EnumException $exception) {
            $httpMethod = HttpMethod::GET();
        }

        $request = new ServerRequest(
            marshalUriFromSapi($serverParams, $headers),
            $httpMethod,
            $body,
            $headers,
            $this->normalizeUploadedFiles($input->files ?? []),
            $serverParams
        );

        if (!empty($input->cookie)) {
            $request = $request->withCookieParams($input->cookie);
        }

        if (!empty($input->get)) {
            $request = $request->withQueryParams($input->get);
        }

        return $request;
    }

    /**
     * @param array $files
     * @return UploadedFile[]
     */
    private function normalizeUploadedFiles(array $files) : array
    {
        $normalizedFiles = [];
        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFileInterface) {
                $normalizedFiles[] = $file;
                continue;
            }
            $normalizedFiles[] = new UploadedFile(
                $file['tmp_name'],
                $file['size'],
                UploadStatus::fromValue($file['error']),
                $file['name'] ?? null,
                $file['type'] ?? null
            );
        }

        return $normalizedFiles;
    }
}
