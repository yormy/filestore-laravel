<?php

namespace Yormy\FilestoreLaravel\Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Upload\Services\FileDestroyer;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertEncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\CleanupTrait;
use Yormy\FilestoreLaravel\Tests\Traits\EncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;

class FileManipulateTest extends TestCase
{
    use AssertEncryptionTrait;
    use CleanupTrait;
    use EncryptionTrait;
    use FileTrait;

    /**
     * @test
     *
     * @group file-manipulate
     */
    public function File_Destroy_FileMissing(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        FileDestroyer::destroyLocal(Storage::disk('local')->path($filename));
        $this->assertFileDoesNotExist(Storage::disk('local')->path($filename));
    }
}
