<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertPdfTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class DeleteTest extends TestCase
{
    use AssertDownloadTrait;
    use AssertPdfTrait;
    use FileTrait;
    use UserTrait;

    /**
     * @test
     *
     * @group delete
     */
    public function pdf_delete_success(): void
    {
        $filename = 'jokes.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->saveToPersistent('myid');

        $this->downloadPdfAndAssertCorrect($xid, $filename);

        $filestore = FilestoreFile::where('xid', $xid)->first();
        $orgDisk = $filestore->disk;
        $orgFullPath = $filestore->full_path;

        $exists = Storage::disk($orgDisk)->exists($orgFullPath);
        $this->assertTrue($exists);

        // ---------- delete ----------
        FilestoreFile::where('xid', $xid)->first()->delete();

        // ---------- assert ----------
        $filestore = FilestoreFile::where('xid', $xid)->first();
        $this->assertNull($filestore);

        $exists = Storage::disk($orgDisk)->exists($orgFullPath);
        $this->assertFalse($exists);
    }
}
