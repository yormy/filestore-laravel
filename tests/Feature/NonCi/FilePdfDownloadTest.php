<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\NonCi;

use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Exceptions\EmbeddingNotAllowedException;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertEncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\CleanupTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FilePdfDownloadTest extends TestCase
{
    use AssertDownloadTrait;
    use AssertEncryptionTrait;
    use CleanupTrait;
    use FileTrait;
    use UserTrait;

    /**
     * @test
     *
     * @group file-pdf-preview
     */
    public function UploadedPdf_PreviewAsImage(): void
    {
        $filename = 'safety_toons.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveToLocal('myid');

        $response = $this->get(route('file.pdf.cover', ['xid' => $xid]));
        $this->assertIsImage($response);
    }

    /**
     * @test
     *
     * @group file-pdf-preview
     */
    public function UploadedPdf_ViewAsPdf(): void
    {
        $filename = 'safety_toons.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveEncryptedToLocal('myid');

        $response = $this->get(route('file.pdf.view', ['xid' => $xid]));
        $this->assertIsPdf($response);
    }

    /**
     * @test
     *
     * @group file-pdf-preview
     */
    public function UploadedPdfPreventEmbedding_View_Exception(): void
    {
        $filename = 'safety_toons.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->preventPdfEmbedding()
            ->saveEncryptedToLocal('myid');

        $this->expectException(EmbeddingNotAllowedException::class);
        $response = $this->get(route('file.pdf.view', ['xid' => $xid]));
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function UploadedPdf_ViewPage(): void
    {
        $filename = 'safety_toons.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveToLocal('myid');

        $response = $this->get(route('file.pdf.page', ['xid' => $xid, 'page' => 2]));
        $this->assertIsImage($response);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function UploadedPdf_ViewPages(): void
    {
        $filename = 'safety_toons.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveToLocal('myid');

        $response = $this->get(route('file.pdf.pages', ['xid' => $xid]));
        $this->assertPagesAsImages($response);
        $this->assertPageCount($response, 3);
    }

    // ---------- HELPERS ---------
    private function assertPagesAsImages($reponse)
    {
        $imagedata = $reponse->getContent();

        $imagedataPages = json_decode($imagedata)->data->files;

        foreach ($imagedataPages as $pageData) {
            $responseMime = substr($pageData, 0, 22);
            $this->assertEquals('data:image/png;base64,', $responseMime);
        }
    }

    private function assertPageCount($reponse, int $pageCount)
    {
        $imagedata = $reponse->getContent();

        $imagedataPages = json_decode($imagedata, true)['data']['files'];
        $this->assertEquals($pageCount, count($imagedataPages));
    }

    private function assertIsImage($response)
    {
        $imagedata = $response->getContent();

        $imagedata = json_decode($imagedata)->data->file;

        $responseMime = substr($imagedata, 0, 22);
        $this->assertEquals('data:image/png;base64,', $responseMime);
    }

    private function assertIsPdf($response)
    {
        $imagedata = $response->getContent();

        $imagedata = json_decode($imagedata)->data->file->data;

        $responseMime = substr($imagedata, 0, 28);
        $this->assertEquals('data:application/pdf;base64,', $responseMime);
    }
}
