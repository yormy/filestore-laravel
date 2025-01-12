<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\Setup\Models\User;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertPdfTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class PdfUploadTest extends TestCase
{
    use AssertDownloadTrait;
    use FileTrait;
    use UserTrait;
    use AssertPdfTrait;

    /**
     * @test
     *
     * @group pdf
     */
    public function Pdf_Upload_Success(): void
    {
        $filename = 'jokes.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->saveToPersistent('myid');

        $this->downloadPdfAndAssertCorrect($xid, $filename);
    }


    /**
     * @test
     *
     * @group pdf
     */
    public function Pdf_UploadEncrypted_Success(): void
    {
        $filename = 'jokes.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->saveEncryptedToPersistent('myid');

        $this->downloadPdfAndAssertCorrect($xid, $filename);
    }




    /*
     * default:
     * makes cover image and variants of cover images
     * converts all pages to img and removes pdf itself
     * // withPdfPAges converts all individual pages to img // must be default
     * $this->markTestSkipped('Encrypted stream assert fails - to implement');
     *
     *
     * ->withPdfPages() : generate a image for each pdf page
     * save pdf also generates first-page-cover for previewing
     */

    /**
     * @test
     *
     * @group file-download
     */
    public function upload_unsupported_mime_exception(): void
    {
        $user = $this->createUser();
        Storage::fake('avatars');

        $this->expectException(ValidationException::class);
        $response = $this->json('POST', route('api.upload', []), [
            'file' => UploadedFile::fake()->image('avatar.jog')->size(300),
        ]);
    }
}
