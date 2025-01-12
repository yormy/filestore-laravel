<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Yormy\FilestoreLaravel\Domain\Shared\Repositories\FilestoreFileAccessRepository;
use Yormy\FilestoreLaravel\Domain\Shared\Services\LoggingHelper;
use Yormy\FilestoreLaravel\Domain\Upload\DataObjects\Enums\MimeTypeEnum;
use Yormy\FilestoreLaravel\Domain\Upload\Services\FileDestroyer;
use Yormy\Xid\Models\Traits\Xid;

class FilestoreFile extends BaseModel
{
    use SoftDeletes;
    use Xid;

    protected $table = 'filestore_files';

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
        'preview_filename',
        'allow_pdf_embedding',
        'access_log',
        'user_encryption',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::deleted(function ($model) {
            FileDestroyer::deleteAll($model);

            $data = LoggingHelper::getLogData(new Request);
            (new FilestoreFileAccessRepository)->createAsDeleted($model, $data);
        });
    }

    public function getFullPathAttribute()
    {
        return $this->getFullPath();
    }

    public function getFullPath(?string $filename = null)
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
