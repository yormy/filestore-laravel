<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Yormy\FilestoreLaravel\Domain\Download\Services\FileGet;
use Yormy\FilestoreLaravel\Domain\Download\Services\FileServe;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileMoveTest extends TestCase
{
    use FileTrait;
    use UserTrait;
    use AssertDownloadTrait;

    /**
     * @test
     *
     * @group file-move
     * @group xxx
     */
    public function LocalFile_MoveEncrypted(): void
    {
        $filename= 'local-unencrypted.txt';
        Storage::disk('local')->put($filename, $this->getContent());
        $path = Storage::disk('local')->path($filename);


        $file = new UploadedFile(
            $path,
            'file.txt',
            'txt',            // MIME type (adjust if not JPEG)
        );

        $xid = UploadFileService::make($file)
            ->saveEncryptedToPersistent('x');

        $localFilename = FileGet::getFile($xid);
        $unencryptedContent = Storage::disk('local')->get($localFilename);

        $this->assertEquals($this->getContent(), $unencryptedContent);
    }


    private function getContent(): string
    {
        return 'Hello this is unencrypted content';
    }
}
