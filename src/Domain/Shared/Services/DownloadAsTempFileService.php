<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadAsTempFileService
{
    /*
     * Download to local for processing
     * Files here are always safe to delete
     */
    public static function get(string $disk, string $fullPath): string
    {
        $data = Storage::disk($disk)->get($fullPath);

        $extension = pathinfo($fullPath)['extension'];

        $tempfilesPath = config('filestore.storage.local.tempfiles', 'tempfiles');
        $tempFilename = $tempfilesPath.DIRECTORY_SEPARATOR.Str::random(50).'.'.$extension;
        Storage::disk('local')->put($tempFilename, $data);

        // Make sure you delete the temp file once you are done

        return $tempFilename;
    }
}
