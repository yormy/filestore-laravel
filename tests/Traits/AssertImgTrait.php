<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Traits;

trait AssertImgTrait
{
    const IMG_ROUTE_DOWNLOAD = 'file.img.download';

    const IMG_ROUTE_VIEW = 'file.img.view';

    protected function streamAndAssertCorrect($xid, $base64, $filename)
    {
        $response = $this->get(route(self::IMG_ROUTE_VIEW, ['xid' => $xid]));
        $imagedata = $response->getContent();

        $imagedata = json_decode($imagedata)->data->file->data;

        $imagedata = str_replace($base64, '', $imagedata);
        $imagedata = base64_decode($imagedata);
        $downloadedFilePath = $this->storeDownloadLocal($imagedata, $filename);

        $this->assertFilesSame($this->getOriginalFilepath($filename), $downloadedFilePath);
    }

    protected function downloadAndAssertCorrect(string $xid, string $filename)
    {
        $response = $this->get(route(self::IMG_ROUTE_DOWNLOAD, ['xid' => $xid]));

        $this->assertCorrectStream($response, $filename);
    }

    protected function downloadAndAssertCorrectAsMember(string $xid, string $filename, $user)
    {
        $response = $this->actingAs($user)->get(route(self::IMG_ROUTE_DOWNLOAD, ['xid' => $xid]));

        $this->assertCorrectStream($response, $filename);
    }
}
