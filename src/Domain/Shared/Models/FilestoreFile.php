<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Yormy\FilestoreLaravel\Domain\Upload\DataObjects\Enums\MimeTypeEnum;
use Yormy\Xid\Models\Traits\Xid;

class FilestoreFile extends BaseModel
{
    use SoftDeletes;
    use Xid;

    protected $table;

    protected $fillable = [
        'xid',
        'user_id',
        'user_type',
        'original_filename',
        'original_extension',
        'size_kb',
        'total_pages',
        'disk',
        'path',
        'filename',
        'mime',
        'is_encrypted',
        'variants',
        'allow_pdf_embedding',
        'access_log',
        'user_encryption',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('filestore.tables.files');
    }

    public function getFullPathAttribute()
    {
        return $this->getFullPath();
    }

    public function getFullPath(string $filename = null)
    {
        if (! $filename) {
            $filename = $this->filename;
        }

        return $this->path.DIRECTORY_SEPARATOR.$filename;
    }

    public function isPdf(): bool
    {
        return $this->mime === MimeTypeEnum::ApplicationPdf->value;
    }
}
