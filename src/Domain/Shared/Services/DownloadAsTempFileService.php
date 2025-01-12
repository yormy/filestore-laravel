<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yormy\FilestoreLaravel\Jobs\CleanupTempJob;

class DownloadAsTempFileService
{
    /*
     * Download to local for processing
     * Files here are always safe to delete
     */
    public static function get(string $disk, string $fullPath): string
    {
        $data = Storage::disk($disk)->get($fullPath);
        $tempFilename = Str::random(50).'.bin';
        Storage::disk('local')->put($tempFilename, $data);

        CleanupTempJob::dispatch('local', $tempFilename)
            ->delay(now()->addMinutes(5));

        return $tempFilename;
    }
}
