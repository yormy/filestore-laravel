<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\Setup\Models\User;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class PdfUploadTest extends TestCase
{
    use AssertDownloadTrait;
    use FileTrait;
    use UserTrait;

    /**
     * @test
     *
     * @group pdf
     * @group xxx
     */
    public function pdf_upload_success(): void
    {
        $filename = 'jokes.pdf';
        $base64 = 'data:image/png;base64,';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->withoutPdfPages()// must be default
            // ->forUser($user)
            ->saveToPersistent('myid');

        // withPdfPAges converts all individual pages to img

        $this->downloadPdfAndAssertCorrect($xid, $filename);
    }

    protected function downloadPdfAndAssertCorrect(string $xid, string $filename)
    {
        $response = $this->get(route('file.pdf.download', ['xid' => $xid]));

        $this->assertCorrectStream($response, $filename);
    }

    /*
     * default:
     * makes cover image and variants of cover images
     * converts all pages to img and removes pdf itself
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
