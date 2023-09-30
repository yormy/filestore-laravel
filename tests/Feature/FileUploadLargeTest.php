<?php

namespace Yormy\FilestoreLaravel\Tests\Feature;

use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileUploadLargeTest extends TestCase
{
    use AssertDownloadTrait;
    use FileTrait;
    use UserTrait;

    public function setUp(): void
    {
        parent::setUp();

        $maxFileSizeKb = 1014 * 1024 * 1024;
        config(['filestore.max_file_size_kb' => $maxFileSizeKb]);
    }

    /**
     * @test
     *
     * @group file-download
     * @group file-large
     */
    public function LargeFile_EncryptDecryptDownload_Success(): void
    {
        $this->markTestSkipped('LargeFiletest Skipped');
        $filename = 'large_pdf_500m.pdf';   // 800 exhausts memory
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveEncryptedToLocal('myid');

        $this->downloadAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group large-file
     */
    public function LargeFile_EncryptDecryptStream_Success(): void
    {
        $this->markTestSkipped('LargeFiletest Skipped');

        $filename = 'large_pdf_200m.pdf'; // 500 exhausts memory
        $base64 = 'data:application/pdf;base64,';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveEncryptedToLocal('myid');

        $this->streamAndAssertCorrect($xid, $base64, $filename);
    }
}
