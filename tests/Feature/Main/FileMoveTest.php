<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Download\Services\FileGet;
use Yormy\FilestoreLaravel\Domain\Upload\Services\MoveFileService;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertImgTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileMoveTest extends TestCase
{
    use AssertDownloadTrait;
    use AssertImgTrait;
    use FileTrait;
    use UserTrait;

    /**
     * @test
     *
     * @group file-move
     */
    public function txt__move_to_persistent_encrypted__success(): void
    {
        $user = $this->createUser();

        // --------- create local file --------
        $localFile = $this->getLocalFilename('text.txt');
        $localContent = file_get_contents($localFile);

        // --------- move --------
        $moveFileService = MoveFileService::make($localFile);
        $moveFileService->encrypted(true);
        $moveFileService->userEncryption($user);
        $xid = $moveFileService->moveToPersistent('abcd');

        // --------- assert --------
        $localFilename = FileGet::getFile(xid: $xid, user: $user); // download file to local

        // --------- assert --------
        $unencryptedContent = Storage::disk('local')->get($localFilename);
        $this->assertEquals($localContent, $unencryptedContent);
        $this->assertFileDoesNotExist($localFile);
    }

    /**
     * @test
     *
     * @group file-move
     */
    public function png__move_persistent__success(): void
    {
        // --------- create local file --------
        $filename = 'sylvester.png';
        $localFile = $this->getLocalFilename($filename);
        $localContent = file_get_contents($localFile);

        // --------- move --------
        $moveFileService = MoveFileService::make($localFile);
        $xid = $moveFileService->moveToPersistent('abcd');

        // -------- assert --------
        $this->assertFalse(file_exists($localFile));

        $base64 = 'data:image/png;base64,';
        $this->streamAndAssertCorrect($xid, $base64, $filename);
    }
}
