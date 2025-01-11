<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Yormy\FilestoreLaravel\Domain\Upload\Services\FileDestroyer;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileDestroyTest extends TestCase
{
    /**
     * @test
     *
     * @group file-download
     * @group xxx
     */
    public function PersistentFile_Destroy_IsGone(): void
    {
        $filename = 'test-upload.txt';
        $file = Storage::disk('digitalocean')->put($filename, 'This is a test file.');

        FileDestroyer::destroyPersistent($filename, 'digitalocean');
        $file = Storage::disk('digitalocean')->get($filename);
        $this->assertNull($file);
    }

}
