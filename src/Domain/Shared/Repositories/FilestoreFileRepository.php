<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Shared\Repositories;

use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;

class FilestoreFileRepository
{
    public function __construct(private ?FilestoreFile $model = null)
    {
        if (! $model) {
            $this->model = new FilestoreFile();
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
}
