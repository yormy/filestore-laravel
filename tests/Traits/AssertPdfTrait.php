<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Traits;

trait AssertPdfTrait
{
    const PDF_ROUTE_DOWNLOAD = 'file.pdf.download';

    protected function downloadPdfAndAssertCorrect(string $xid, string $filename)
    {
        $response = $this->get(route(self::PDF_ROUTE_DOWNLOAD, ['xid' => $xid]));

        $this->assertCorrectStream($response, $filename);
    }
}
