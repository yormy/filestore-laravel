<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Yormy\Xid\Models\Traits\Xid;

class FilestoreKey extends BaseModel
{
    use SoftDeletes;
    use Xid;

    protected $table = 'filestore_keys';

    protected $fillable = [
        'xid',
        'user_id',
        'user_type',
        'key',
    ];
}
