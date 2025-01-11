<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Upload\Services\FileDestroyer;
use Yormy\FilestoreLaravel\Tests\TestCase;

class FileDestroyTest extends TestCase
{
    /**
     * @test
     *
     * @group file-download
     * @group xxx
     */
    public function persistent_file_destroy_is_gone(): void
    {
        $filename = 'test-upload.txt';
        $file = Storage::disk('digitalocean')->put($filename, 'This is a test file.');

        FileDestroyer::destroyPersistent($filename, 'digitalocean');
        $file = Storage::disk('digitalocean')->get($filename);
        $this->assertNull($file);
    }
}
