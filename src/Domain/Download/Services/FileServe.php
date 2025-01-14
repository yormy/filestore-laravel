<?php

namespace Yormy\FilestoreLaravel\Domain\Download\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yormy\FilestoreLaravel\Domain\Encryption\FileVault;
use Yormy\FilestoreLaravel\Domain\Shared\Enums\FileEncryptionExtension;
use Yormy\FilestoreLaravel\Domain\Shared\Enums\ServeType;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\FilestoreLaravel\Domain\Shared\Repositories\FilestoreFileAccessRepository;
use Yormy\FilestoreLaravel\Domain\Shared\Repositories\FilestoreFileRepository;
use Yormy\FilestoreLaravel\Domain\Shared\Services\DownloadAsTempFileService;
use Yormy\FilestoreLaravel\Domain\Shared\Services\LoggingHelper;
use Yormy\FilestoreLaravel\Domain\Upload\DataObjects\Enums\MimeTypeEnum;
use Yormy\FilestoreLaravel\Domain\Upload\Services\PdfImageService;
use Yormy\FilestoreLaravel\Exceptions\EmbeddingNotAllowedException;
use Yormy\FilestoreLaravel\Exceptions\FileGetException;
use Yormy\FilestoreLaravel\Jobs\CleanupTempJob;
use Yormy\FilestoreLaravel\Rules\ValidVariant;
use Yormy\FilestoreLaravel\Traits\DiskHelperTrait;
use Yormy\Xid\Services\XidService;

class FileServe extends FileBase
{
    use DiskHelperTrait;

    public static function view(Request $request, string $xid, ServeType $serveType = ServeType::URL, ?string $variant = null): array
    {
        ValidVariant::validate($variant);

        $fileRecord = self::getFileRecord($request, $xid);

        if ($fileRecord->isPdf() && ! $fileRecord->allow_pdf_embedding) {
            throw new EmbeddingNotAllowedException;
        }

        $filename = self::getFilename($variant, $fileRecord);
        $mime = $fileRecord->mime;

        $data = self::display($filename, $fileRecord->disk, $mime, $fileRecord);

        if ($serveType === ServeType::DATA) {
            return self::serveAsData($fileRecord, $data);
        }
        if ($serveType === ServeType::URL) {
            self::serveAsUrl($fileRecord, $data); // exits
        }

    }

    public static function viewCover(Request $request, string $xid, ?string $variant = null): string
    {
        ValidVariant::validate($variant);

        $fileRecord = self::getFileRecord($request, $xid);

        $filename = self::getFilename($variant, $fileRecord);
        $mime = $fileRecord->mime;

        if ($fileRecord->isPdf()) {
            $filename .= '.png';
            $mime = MimeTypeEnum::ImagePng->value;
        }

        return self::display($filename, $fileRecord->disk, $mime, $fileRecord);
    }

    public static function page(Request $request, string $xid, int $pageNr): string
    {
        $fileRecord = self::getFileRecord($request, $xid);

        $filename = PdfImageService::createFilename($pageNr);
        $path =
            $fileRecord->path.
            DIRECTORY_SEPARATOR.
            PdfImageService::PATH_PAGES.
            DIRECTORY_SEPARATOR.
            $fileRecord->filename.
            $filename.'.png';

        $mime = MimeTypeEnum::ImagePng->value;

        return self::display($path, $fileRecord->disk, $mime, $fileRecord);
    }

    public static function pages(Request $request, string $xid): array
    {
        $fileRecord = self::getFileRecord($request, $xid);
        $pageCount = $fileRecord->total_pages;

        $i = 1;
        $pages = [];
        while ($i <= $pageCount) {
            $pages[$i] = self::page($request, $xid, $i);
            $i++;
        }

        return $pages;
    }

    public static function serveAsData(FilestoreFile $fileRecord, string $data): array
    {
        $data = [
            'height' => $fileRecord->height,
            'width' => $fileRecord->width,
            'data' => $data,
        ];

        return $data;
    }

    #[NoReturn]
    public static function serveAsUrl(FilestoreFile $fileRecord, string $data): void
    {
        // serve as plain http link
        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $data); // Remove prefix
        $imageData = base64_decode($base64Image, true);
        if ($imageData === false) {
            exit('Invalid Base64 data');
        }

        header('Content-Type: '.$fileRecord->mime);
        header('Content-Length: '.strlen($imageData));
        header('Content-Disposition: inline; filename="image.png"');

        echo $imageData;
        exit;
    }

    private static function getFileRecord(Request $request, string $xid): FilestoreFile
    {
        XidService::validateOrFail($xid);

        $fileRecord = (new FilestoreFileRepository)->getByXid($xid);

        $data = LoggingHelper::getLogData($request);
        $filestoreFileAccessRepository = new FilestoreFileAccessRepository;
        $filestoreFileAccessRepository->createAsViewed($fileRecord, $data);

        return $fileRecord;
    }

    private static function display(string $filename, string $disk, string $mime, $fileRecord)
    {
        $encryptionKey = self::getKey($fileRecord);

        if (self::isEncrypted($filename)) {
            return self::displayEncrypted($disk, $filename, $mime, $encryptionKey);
        }

        return self::displayPlain($disk, $filename, $mime);
    }

    public static function download(Request $request, string $xid, ?string $variant = null, ?string $downloadAs = null)
    {
        ValidVariant::validate($variant);

        XidService::validateOrFail($xid);

        $fileRecord = self::getFileRecord($request, $xid);

        $data = LoggingHelper::getLogData($request);
        $filestoreFileAccessRepository = new FilestoreFileAccessRepository;
        $filestoreFileAccessRepository->createAsDownloaded($fileRecord, $data);

        $filename = self::getFilename($variant, $fileRecord);

        if (! $downloadAs) {
            $downloadAs = basename($filename);
        }

        if (! $downloadAs) {
            $extension = FileEncryptionExtension::SYSTEM->value;
            $downloadAs = str_replace($extension, '', $filename);
            $downloadAs = basename($downloadAs);
        }

        $encryptionKey = self::getKey($fileRecord);

        if (self::isEncrypted($filename)) {
            return self::downloadEncrypted($fileRecord->disk, $filename, $downloadAs, $encryptionKey);
        }

        return self::downloadPlain($fileRecord->disk, $filename, $downloadAs);

    }

    private static function downloadPlain(string $disk, string $fullPath, string $downloadAs): StreamedResponse
    {
        return Storage::disk($disk)->download($fullPath, $downloadAs);
    }

    private static function displayPlain(string $disk, string $fullPath, string $mime)
    {
        $imagedata = Storage::disk($disk)->get($fullPath);
        if (! $imagedata) {
            throw new FileGetException("Cannot get $fullPath from $disk");
        }

        return self::convertBase64($imagedata, $mime);
    }

    private static function downloadEncrypted(string $disk, string $fullPath, string $downloadAs, ?string $encryptionKey = null)
    {
        $localFilename = $fullPath;
        if (! self::isLocalFilesystem($disk)) {
            $localFilename = DownloadAsTempFileService::get($disk, $fullPath); // Force download to local to decrypt
        }

        return response()->streamDownload(function () use ($localFilename, $encryptionKey) {
            (new FileVault)->disk('local')->streamDecrypt($localFilename, $encryptionKey);
        }, $downloadAs);
    }

    private static function displayEncrypted(string $disk, string $fullPath, string $mime, ?string $encryptionKey = null)
    {
        $localFilename = $fullPath;
        if (! self::isLocalFilesystem($disk)) {
            $localFilename = DownloadAsTempFileService::get($disk, $fullPath); // Force download to local to decrypt
        }

        ob_start();
        (new FileVault)->disk('local')->streamDecrypt($localFilename, $encryptionKey);
        $imagedata = ob_get_contents();
        ob_end_clean();

        CleanupTempJob::dispatch('local', $localFilename);

        return self::convertBase64($imagedata, $mime);
    }

    private static function convertBase64(string $imagedata, string $mime): string
    {
        $prefix = "data:$mime;base64,";

        return $prefix.base64_encode($imagedata);
    }
}
