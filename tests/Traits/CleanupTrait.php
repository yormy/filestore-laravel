<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Traits;

use Illuminate\Support\Facades\Storage;

trait CleanupTrait
{
    //    protected function cleanupFiles($filenames)
    //    {
    //        foreach ($filenames as $filename) {
    //            Storage::disk('local')->delete($filename);
    //            Storage::disk('local')->delete($this->getEncryptedFilename($filename));
    //        }
    //    }

    public function tearDown(): void
    {
        Storage::disk('local')->deleteDirectory('downloads');
        Storage::disk('local')->deleteDirectory('uploads');
        Storage::disk('local')->deleteDirectory('/');

        parent::tearDown();
    }
}
