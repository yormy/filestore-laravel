<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Traits;

use Illuminate\Http\Response;

trait AssertPdfTrait
{
    const PDF_ROUTE_DOWNLOAD = 'file.pdf.download';

    const PDF_ROUTE_DOWNLOAD_PAGE = 'file.pdf.page';

    protected function downloadPdfAndAssertCorrect(string $xid, string $filename, $user = null)
    {
        $httpcall = $this;
        if ($user) {
            $httpcall = $this->actingAs($user);
        }

        $response = $httpcall->get(route(self::PDF_ROUTE_DOWNLOAD, ['xid' => $xid]));

        $this->assertCorrectStream($response, $filename);
    }

    protected function downloadPdfPageAndAssertCorrect(string $xid, string $filename, $user = null, $pageNr = null)
    {
        $httpcall = $this;
        if ($user) {
            $httpcall = $this->actingAs($user);
        }

        $response = $httpcall->json('GET', route(self::PDF_ROUTE_DOWNLOAD_PAGE, ['xid' => $xid, 'page' => $pageNr]));
        $response->assertStatus(Response::HTTP_OK);

        $content = json_decode($response->getContent());

        $this->assertStringContainsString('data:image/png;base64', $content->data->file);
    }
}
