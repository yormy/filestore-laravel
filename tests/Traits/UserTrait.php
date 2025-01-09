<?php

namespace Yormy\FilestoreLaravel\Tests\Traits;

use Yormy\FilestoreLaravel\Domain\Encryption\FileVault;
use Yormy\FilestoreLaravel\Tests\Setup\Models\User;

trait UserTrait
{
    private function createUser()
    {
        $user = User::create([
            'email' => 'test@exampel.com',
            'encryption_key' => 'base64:'.base64_encode((new FileVault)->generateKey()),
        ]);

        return $user;
    }
}
