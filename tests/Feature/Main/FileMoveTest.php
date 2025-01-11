<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Download\Services\FileGet;
use Yormy\FilestoreLaravel\Domain\Upload\Services\MoveFileService;
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
    public function localFile_MoveToPersistentEncrypted_Success(): void
    {
        $user = $this->createUser();

        // --------- create local file --------
        $localFile = $this->getLocalFilename('text.txt');
        $localContent = file_get_contents($localFile);

        // --------- move --------
        $moveFileService = MoveFileService::make($localFile);
        $moveFileService->encrypted(true);
        $moveFileService->userEncryption($user);

        $xid = $moveFileService->moveToPersistent('abcd'); /// always moves to under 1 in abcd ?

        // --------- assert --------
        $localFilename = FileGet::getFile(xid: $xid, user: $user);
        $unencryptedContent = Storage::disk('local')->get($localFilename);

        $this->assertEquals($localContent, $unencryptedContent);
        $this->assertFileDoesNotExist($localFile);
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
