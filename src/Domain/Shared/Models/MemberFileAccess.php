<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Models;

class MemberFileAccess extends BaseModel
{
    protected $table;

    protected $fillable = [
        'filestore_file_id',
        'user_id',
        'user_type',
        'as_download',
        'as_view',
        'ip',
        'useragent',
        'as_download',
        'as_view',
    ];

    protected $casts = [
        'as_download' => 'boolean',
        'as_view' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('filestore.tables.access');
    }
}
