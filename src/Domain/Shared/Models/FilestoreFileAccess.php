<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Models;

class FilestoreFileAccess extends BaseModel
{
    protected $table = 'filestore_files_access';

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
}
