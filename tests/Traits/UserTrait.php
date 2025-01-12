<?php

namespace Yormy\FilestoreLaravel\Tests\Traits;

use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreKey;
use Yormy\FilestoreLaravel\Tests\Setup\Models\User;

trait UserTrait
{
    private function createUser(string $key = null)
    {
        $user = User::create([
            'email' => 'test@exampel.com',
        ]);

        (new FilestoreKey)->createForUser($user, $key);

        return $user;
    }
}
