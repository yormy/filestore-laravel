<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Shared\Repositories;

use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;

class FilestoreFileRepository
{
    public function __construct(private ?FilestoreFile $model = null)
    {
        if (! $model) {
            $this->model = new FilestoreFile;
        }
    }

    public function getByXid(string $xid): FilestoreFile
    {
        return FilestoreFile::where('xid', $xid)->first();
    }

    public function getByName(string $name): FilestoreFile
    {
        return FilestoreFile::where('name', $name)->first();
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

    public function syncVariantsEncryptionExtensions(FilestoreFile $fileRecord, string $mainFilename): void
    {
        $extension = '.'.pathinfo($mainFilename)['extension'];
        $variants = $fileRecord->variants;
        foreach ($variants as $key => $variant) {
            $variants[$key]['filename'] = $variants[$key]['filename'].$extension;
        }

        $fileRecord->variants = $variants;
        $fileRecord->save();
    }



}
