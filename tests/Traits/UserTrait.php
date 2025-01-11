<?php

namespace Yormy\FilestoreLaravel\Tests\Traits;

use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreKey;
use Yormy\FilestoreLaravel\Tests\Setup\Models\User;

trait UserTrait
{
    private function createUser()
    {
        $user = User::create([
            'email' => 'test@exampel.com',
        ]);

        (new FilestoreKey)->createForUser($user);

        return $user;
    }
}
