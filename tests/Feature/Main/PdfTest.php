<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Yormy\FilestoreLaravel\Domain\Upload\Services\MoveFileService;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertPdfTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class PdfTest extends TestCase
{
    use AssertDownloadTrait;
    use AssertPdfTrait;
    use FileTrait;
    use UserTrait;

    /**
     * @test
     *
     * @group pdf
     */
    public function pdf_upload_success(): void
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
    public function pdf_upload_encrypted_success(): void
    {
        $filename = 'jokes.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->saveEncryptedToPersistent('myid');

        $this->downloadPdfAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group pdf
     */
    public function pdf_upload_encrypted_userkey_success(): void
    {
        $filename = 'jokes.pdf';
        $file = $this->buildFile($filename);
        $user = $this->createUser();

        $xid = UploadFileService::make($file)
            ->userEncryption($user)
            ->saveEncryptedToPersistent('myid');

        $this->downloadPdfAndAssertCorrect($xid, $filename, $user);
    }

    /**
     * @test
     *
     * @group pdf
     */
    public function pdf_pages_as_img_success(): void
    {
        $filename = 'jokes.pdf';
        $file = $this->buildFile($filename);
        $user = $this->createUser();

        $xid = UploadFileService::make($file)
            ->withPdfPages()
            ->saveToLocal('myid');

        $this->downloadPdfAndAssertCorrect($xid, $filename, $user);
        $this->downloadPdfPAgeAndAssertCorrect($xid, $filename, $user, 5);
    }

    /**
     * @test
     *
     * @group pdf
     */
    public function pdf_pages_as_img_persistent_success(): void
    {
        $filename = 'jokes.pdf';
        $file = $this->buildFile($filename);
        $user = $this->createUser();

        $xid = UploadFileService::make($file)
            ->withPdfPages()
            ->saveToPersistent('myid');

        $this->downloadPdfAndAssertCorrect($xid, $filename, $user);
    }

    /*
     *             ->sanitize()
            ->forUser($user)

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

    /**
     * @test
     *
     * @group file-move
     * @group xxx
     */
    public function pdf_move_to_persistent_success(): void
    {
        $filename = 'jokes.pdf';
        $user = $this->createUser();

        // --------- create local file --------
        $localFile = $this->getLocalFilename($filename);
        $localContent = file_get_contents($localFile);

        // --------- move --------
        $moveFileService = MoveFileService::make($localFile);
        $xid = $moveFileService->moveToPersistent('abcd');

        // -------- assert --------
        $this->assertFalse(file_exists($localFile));
        $this->downloadPdfAndAssertCorrect($xid, $filename, $user);
    }
}
