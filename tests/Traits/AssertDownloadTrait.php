<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Traits;

trait AssertDownloadTrait
{
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
