<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Upload\Services;

use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\FilestoreLaravel\Observers\Events\FileDestroyedEvent;

class FileDestroyer
{
    public static function deleteAll(FileStoreFile $fileStoreFile): void
    {
        $filesToDelete = self::generateFilelistToDelete($fileStoreFile);
        foreach ($filesToDelete as $filename) {
            FileDestroyer::destroyPersistent($filename, $fileStoreFile->disk);
        }
    }

    public static function destroyLocal(string $localFilename)
    {
        $size = filesize($localFilename);

        // step 1: delete the file
        unlink($localFilename);

        // step 2: overwrite the file with new data
        $fp = fopen($localFilename, 'w+');
        fwrite($fp, str_repeat('x', $size), $size);

        // step 3: create a smaller file and overwrite to break fat alignments
        $smallerSize = (int) round($size * 0.8);
        $fp = fopen($localFilename, 'w+');
        fwrite($fp, str_repeat('t', $smallerSize), $smallerSize);

        // step 4: create a larger file and overwrite to break fat alignments
        $largerSize = (int) round($size * 1.2);
        $fp = fopen($localFilename, 'w+');
        fwrite($fp, str_repeat('t', $largerSize), $largerSize);

        // step 5: delete the dummy file
        unlink($localFilename);

        event(new FileDestroyedEvent($localFilename));
    }

    public static function destroyPersistent(string $filename, string $disk): bool
    {
        $size = Storage::disk($disk)->fileSize($filename);

        // step 1: delete the file
        $success = Storage::disk($disk)->delete($filename);
        if (! $success) {
            return false;
        }

        // step 2: overwrite the file with new data
        $success = Storage::disk($disk)->put($filename, str_repeat('x', $size));
        if (! $success) {
            return false;
        }

        // step 3: create a smaller file and overwrite to break fat alignments
        $smallerSize = (int) round($size * 0.8);
        $success = Storage::disk($disk)->put($filename, str_repeat('x', $smallerSize));
        if (! $success) {
            return false;
        }

        // step 4: create a larger file and overwrite to break fat alignments
        $largerSize = (int) round($size * 1.2);
        $success = Storage::disk($disk)->put($filename, str_repeat('x', $largerSize));
        if (! $success) {
            return false;
        }

        // step 5: delete the dummy file
        $success = Storage::disk($disk)->delete($filename);
        if (! $success) {
            return false;
        }

        event(new FileDestroyedEvent($filename, $disk));

        return true;
    }

    private static function generateFilelistToDelete(FileStoreFile $filestoreFile): array
    {
        $filesToDelete = [];
        $filesToDelete[] = $filestoreFile->fullPath;

        if ($filestoreFile->preview_filename) {
            $filesToDelete[] = $filestoreFile->preview_filename;
        }

        $variants = json_decode($filestoreFile->variants);
        foreach ($variants as $variant) {
            $filesToDelete[] = $filestoreFile->path. DIRECTORY_SEPARATOR. $variant->filename;
        }

        return $filesToDelete;
    }
}
