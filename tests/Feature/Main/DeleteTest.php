<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use League\Flysystem\UnableToRetrieveMetadata;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\FilestoreLaravel\Domain\Upload\Services\FileDestroyer;
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
     * @group xxx
     */
    public function pdf_delete_success(): void
    {
        $filename = 'jokes.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->saveToPersistent('myid');

        $this->downloadPdfAndAssertCorrect($xid, $filename);

        // ---------- delete ----------
        $filesToDelete = $this->generateFilelistToDelete($xid);
        foreach ($filesToDelete as $filename) {
            $forcedPersistentDisk = 'digitalocean';
            FileDestroyer::destroyPersistent($filename, $forcedPersistentDisk); // this just deletes 1 file specified
        }

        // ---------- assert ----------
        $this->expectException(UnableToRetrieveMetadata::class);
        $this->downloadPdfAndAssertCorrect($xid, $filename);
    }

    private function generateFilelistToDelete(string $xid): array
    {
        $filestore = FilestoreFile::where('xid', $xid)->firstOrFail();

        $filesToDelete = [];
        $filesToDelete[] = $filestore->fullPath;

        if ($filestore->preview_filename) {
            $filesToDelete[] = $filestore->preview_filename;
        }


        $variants = json_decode($filestore->variants);
        foreach ($variants as $variant) {
            $filesToDelete[] = $filestore->path. DIRECTORY_SEPARATOR. $variant->filename;
        }

        return $filesToDelete;
    }
}
