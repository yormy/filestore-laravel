<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Download\Services\FileGet;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileMoveTest extends TestCase
{
    use FileTrait;
    use UserTrait;

    /**
     * @test
     *
     * @group file-move
     * @group xxx
     */
    public function local_file_move_encrypted(): void
    {
        $localFile = $this->getLocalFilename('text.txt');

        $file = new UploadedFile(
            $localFile,
            basename($localFile),
        );

        // todo : move
        $xid = UploadFileService::make($file)
            ->saveEncryptedToPersistent('x');

        $localFilename = FileGet::getFile($xid);
        $unencryptedContent = Storage::disk('local')->get($localFilename);

        $this->assertEquals(file_get_contents($localFile), $unencryptedContent);
    }

    // -------- HELPERS --------

    private function getLocalFilename($filename): string
    {
        $localPath = $this->getOriginalFilepath($filename);

        // make a copy to work with, and can test deletion
        $filename = 'localcopy-'.$filename;
        Storage::disk('local')->put($filename, file_get_contents($localPath));

        return Storage::disk('local')->path($filename);
    }
}
