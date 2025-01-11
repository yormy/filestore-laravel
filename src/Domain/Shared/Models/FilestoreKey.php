<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Models;

use Yormy\FilestoreLaravel\Domain\Encryption\FileVault;
use Yormy\Xid\Models\Traits\Xid;

class FilestoreKey extends BaseModel
{
    use Xid;

    protected $table = 'filestore_keys';

    protected $fillable = [
        'xid',
        'user_id',
        'user_type',
        'key',
    ];

    public function createForUser($user, $key = null)
    {
        if (! $key) {
            $key = 'base64:'.base64_encode((new FileVault)->generateKey());
        }

        return FilestoreKey::create([
            'user_id' => $user->id,
            'user_type' => get_class($user),
            'key' => $key,
        ]);
    }
}
