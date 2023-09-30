<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Traits;

use Illuminate\Support\Facades\Storage;

trait AssertEncryptionTrait
{
    protected function assertReadable(string $filename, string $contents)
    {
        $readData = Storage::disk('local')->get($filename);
        $this->assertStringContainsString($contents, $readData);
    }

    protected function assertEncrypted(string $filename, string $contents)
    {
        $readData = Storage::disk('local')->get($filename);
        $this->assertStringNotContainsString($contents, $readData);
    }
}
