<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\Setup\Models\User;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertImgTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileUploadLargeTest extends TestCase
{
    use AssertDownloadTrait;
    use AssertImgTrait;
    use FileTrait;
    use UserTrait;

    protected function setUp(): void
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
    public function large_file_encrypt_decrypt_download_success(): void
    {
        $this->markTestSkipped('LargeFiletest Skipped');
        $filename = 'large_pdf_500m.pdf';   // 800 exhausts memory
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveEncryptedToLocal('myid');

        $this->downloadAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group large-file
     */
    public function large_file_encrypt_decrypt_stream_success(): void
    {
        $this->markTestSkipped('LargeFiletest Skipped');

        $filename = 'large_pdf_200m.pdf'; // 500 exhausts memory
        $base64 = 'data:application/pdf;base64,';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveEncryptedToLocal('myid');

        $this->streamAndAssertCorrect($xid, $base64, $filename);
    }
}
