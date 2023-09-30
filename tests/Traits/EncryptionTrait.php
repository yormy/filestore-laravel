<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Traits;

use Facades\Yormy\FilestoreLaravel\Domain\Encryption\FileVault;

trait EncryptionTrait
{
    protected function generateRandomKey()
    {
        return 'base64:'.base64_encode(FileVault::generateKey());
    }
}
