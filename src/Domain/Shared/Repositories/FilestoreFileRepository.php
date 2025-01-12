<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Shared\Repositories;

use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\FilestoreLaravel\Domain\Upload\Services\FileDestroyer;

class FilestoreFileRepository
{
    public function __construct(private ?FilestoreFile $model = null)
    {
        if (! $model) {
            $this->model = new FilestoreFile;
        }
    }

    public function create(array $defaults): FilestoreFile
    {
        return $this->model->create($defaults);
    }

    public function update(FilestoreFile $model, array $data): FilestoreFile
    {
        $model->update($data);

        return $model;
    }

    public function setPreviewFilename(FilestoreFile $model, string $filename): FilestoreFile
    {
        $model->preview_filename = $filename;
        $model->save();

        return $model;
    }

    public function destroy(string $xid)
    {
        $filestoreFile = FilestoreFile::where('xid', $xid)->firstOrFail();

        $filesToDelete = $this->generateFilelistToDelete($filestoreFile);
        foreach ($filesToDelete as $filename) {
            $forcedPersistentDisk = 'digitalocean';
            FileDestroyer::destroyPersistent($filename, $forcedPersistentDisk);
        }

        $filestoreFile->delete();
    }

    private static function generateFilelistToDelete(FileStoreFile $filestoreFile): array
    {
        $filesToDelete = [];
        $filesToDelete[] = $filestoreFile->fullPath;

        if ($filestoreFile->preview_filename) {
            $filesToDelete[] = $filestoreFile->preview_filename;
        }


        $variants = json_decode($filestoreFile->variants);
        foreach ($variants as $variant) {
            $filesToDelete[] = $filestoreFile->path. DIRECTORY_SEPARATOR. $variant->filename;
        }

        return $filesToDelete;
    }
}
