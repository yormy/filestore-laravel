<?php

namespace Yormy\FilestoreLaravel\Domain\Download\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yormy\FilestoreLaravel\Domain\Encryption\FileVault;
use Yormy\FilestoreLaravel\Domain\Shared\Enums\FileEncryptionExtension;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\FilestoreLaravel\Domain\Shared\Repositories\FilestoreFileAccessRepository;
use Yormy\FilestoreLaravel\Domain\Upload\DataObjects\Enums\MimeTypeEnum;
use Yormy\FilestoreLaravel\Domain\Upload\Services\PdfImageService;
use Yormy\FilestoreLaravel\Exceptions\EmbeddingNotAllowedException;
use Yormy\FilestoreLaravel\Exceptions\FileGetException;
use Yormy\FilestoreLaravel\Exceptions\InvalidVariantException;
use Yormy\Xid\Services\XidService;

class FileGet extends FileBase
{
    public static function getFile(string $xid, ?string $variant = null, ?string $downloadAs = null)
    {
        XidService::validateOrFail($xid);
        $fileRecord = FilestoreFile::where('xid', $xid)->firstOrFail();
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

            $content = Storage::disk($fileRecord->disk)->get($fileRecord->fullpath);
            file_put_contents('local-copy.txt', $content);

            $localfile = 'local-copy.txt.xfile';
            Storage::disk('local')->put($localfile, $content);

            $localPath = Storage::disk('local')->path($localfile);

            return (new FileVault)->disk('local')->decrypt($localfile, $encryptionKey);

        }

        return self::downloadPlain($fileRecord->disk, $filename, $downloadAs);
    }


}
