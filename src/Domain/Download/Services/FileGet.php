<?php

namespace Yormy\FilestoreLaravel\Domain\Download\Services;

use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Encryption\FileVault;
use Yormy\FilestoreLaravel\Domain\Shared\Enums\FileEncryptionExtension;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\Xid\Services\XidService;

class FileGet extends FileBase
{
    public static function getFile(string $xid, $user = null, ?string $variant = null, ?string $downloadAs = null)
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

        $encryptionKey = self::getKey($fileRecord, $user);

        if (self::isEncrypted($filename)) {
            return self::downloadEncrypted($fileRecord->disk, $fileRecord->fullpath, $encryptionKey);
        }

        return self::downloadPlain($fileRecord->disk, $filename, $downloadAs);
    }

    private static function downloadEncrypted(string $disk, string $fullPath, ?string $encryptionKey = null)
    {
        $localFilename = basename($fullPath);
        $content = Storage::disk($disk)->get($fullPath);
        Storage::disk('local')->put($localFilename, $content);

        return (new FileVault)->disk('local')->decrypt(sourceFile: $localFilename, key: $encryptionKey);
    }

    private static function downloadPlain(string $disk, string $fullPath, ?string $encryptionKey = null)
    {
        $localfile = 'downloadas as.txt';
        $content = Storage::disk($disk)->get($fullPath);
        Storage::disk('local')->put($localfile, $content);

        return $localfile;
    }
}
