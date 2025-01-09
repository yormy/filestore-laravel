<?php

namespace Yormy\FilestoreLaravel\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertEncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\CleanupTrait;
use Yormy\FilestoreLaravel\Tests\Traits\EncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;

class FileUploadLargeTest extends TestCase
{
    use AssertDownloadTrait;
    use AssertEncryptionTrait;
    use CleanupTrait;
    use EncryptionTrait;
    use FileTrait;

    //    /**
    //     * @test
    //     *
    //     * @group file-upload
    //     */
    //    public function LargeFile_Upload_DownloadReadable(): void
    //    {
    //        $maxFileSizeKb = 1014*500;
    //        config(['filestore.max_file_size_kb' => $maxFileSizeKb]);
    //        $filename = $this->createFile($maxFileSizeKb);
    //
    //        $file = new UploadedFile($filename, $filename);
    //
    //        $fh = fopen( $filename, "r" );
    //        $originalContents = fread( $fh, 100 );
    //        fclose( $fh );
    //
    //        $xid = UploadFileService::make($file)
    //            ->sanitize()
    //            ->memberId(6)
    //            ->saveToLocal('myid');
    //
    //        $this->assertUploadedReadable($xid, $originalContents);
    //
    //        $response = $this->get(route('api.download', ['xid' => $xid]));
    //
    //        $downloadedContent = $response->streamedContent();
    //        $this->assertReadable($originalContents, $downloadedContent);
    //
    //        $downloadedFilePath = $this->storeDownloadLocal($downloadedContent, $filename);
    //        $this->assertFileSame($filename, $downloadedFilePath);
    //
    //    }
    //
    //    /**
    //     * @test
    //     *
    //     * @group file-upload
    //     */
    //    public function UploadedUnencryptedLocal_Download_Correct(): void
    //    {
    //        $this->writeFile();
    //        $maxFileSizeKb = 10; // 1014*1024*2;
    //        config(['filestore.max_file_size_kb' => $maxFileSizeKb]);
    //
    //        $filename = 'generated_1gb.txt';
    //        $file = $this->buildFile($filename);
    //
    //        $xid = UploadFileService::make($file)
    //            ->sanitize()
    //            ->memberId(6)
    //            ->saveToLocal('myid');
    //
    //        $this->downloadAndAssertCorrect($xid, $filename);
    //    }

    /**
     * @test
     *
     * @group file-large
     */
    public function LargeFile_UploadEncrypted_DownloadReadable(): void
    {
        $this->markTestSkipped('LargeFiletest Skipped');

        $maxFileSizeKb = 1014 * 10;
        config(['filestore.max_file_size_kb' => $maxFileSizeKb]);

        $filename = $this->createFile($maxFileSizeKb);
        $file = new UploadedFile($filename, $filename);

        $fh = fopen($filename, 'r');
        $originalContents = fread($fh, 100);
        fclose($fh);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveEncryptedToLocal('myid');

        $this->assertUploadedEncrypted($xid, $originalContents);

        $response = $this->get(route('api.download', ['xid' => $xid]));

        $downloadedContent = $response->streamedContent();
        $this->assertReadable($originalContents, $downloadedContent);

        $downloadedFilePath = $this->storeDownloadLocal($downloadedContent, $filename);
        $this->assertFileSame($filename, $downloadedFilePath);
    }

    // --------- HELPERS --------
    private function assertUploadedEncrypted(string $xid, $originalContents)
    {
        $uploadedContent = $this->getUploadedContent($xid);

        $this->assertEncrypted($originalContents, $uploadedContent);
    }

    private function assertUploadedReadable(string $xid, $originalContents)
    {
        $uploadedContent = $this->getUploadedContent($xid);

        $this->assertReadable($originalContents, $uploadedContent);
    }

    private function assertReadable(string $originalContents, string $uploadedContent)
    {
        $this->assertStringContainsString($originalContents, $uploadedContent);
    }

    private function assertEncrypted(string $originalContents, string $uploadedContent)
    {
        $this->assertStringNotContainsString($originalContents, $uploadedContent);
    }

    private function assertFileSame(string $filename, string $downloadedFilePath)
    {
        $this->assertTrue(sha1_file($filename) === sha1_file($downloadedFilePath), 'Files are not the same');
    }

    private function getUploadedContent(string $xid): string
    {
        $fileRecord = FilestoreFile::where('xid', $xid)->firstOrFail();
        $fullPath = $fileRecord->getFullPath();

        return Storage::disk('local')->get($fullPath);
    }
}
