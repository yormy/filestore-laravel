<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Traits;

trait AssertDownloadTrait
{
    protected function streamAndAssertCorrect($xid, $base64, $filename)
    {
        $response = $this->get(route('file.img.view', ['xid' => $xid]));
        $imagedata = $response->getContent();

        $imagedata = json_decode($imagedata)->data->file->data;

        $imagedata = str_replace($base64, '', $imagedata);
        $imagedata = base64_decode($imagedata);
        $downloadedFilePath = $this->storeDownloadLocal($imagedata, $filename);

        $this->assertFilesSame($this->getOriginalFilepath($filename), $downloadedFilePath);
    }

    protected function downloadAndAssertCorrect(string $xid, string $filename)
    {
        $response = $this->get(route('file.img.download', ['xid' => $xid]));

        $this->assertCorrectStream($response, $filename);
    }

    protected function downloadAndAssertCorrectAsMember(string $xid, string $filename, $member)
    {
        $response = $this->actingAs($member)->get(route('file.img.download', ['xid' => $xid]));
        $this->assertCorrectStream($response, $filename);
    }

    protected function assertCorrectStream($response, $filename)
    {
        $downloadedContent = $response->streamedContent();
        $downloadedFilePath = $this->storeDownloadLocal($downloadedContent, $filename);

        $this->assertFilesSame($this->getOriginalFilepath($filename), $downloadedFilePath);
    }

    private function assertFilesSame($originalFile, $compareWithFile)
    {
        $this->assertTrue(sha1_file($originalFile) === sha1_file($compareWithFile), 'Downloaded content is not the same as uploaded');
    }
}
