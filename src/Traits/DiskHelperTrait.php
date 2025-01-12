<?php declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Traits;

trait DiskHelperTrait
{
    protected static function isLocalFilesystem($disk)
    {
        $filesystem = config('filesystems.disks.'.$disk.'.driver');

        return $filesystem === 'local';
    }
}
