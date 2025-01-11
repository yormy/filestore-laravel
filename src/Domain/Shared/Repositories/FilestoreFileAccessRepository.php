<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Shared\Repositories;

use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFileAccess;

class FilestoreFileAccessRepository
{
    public function __construct(private ?FilestoreFileAccess $model = null)
    {
        if (! $model) {
            $this->model = new FilestoreFileAccess;
        }
    }

    public function createAsDownloaded(FilestoreFile $filestoreFile, array $logData): ?FilestoreFileAccess
    {
        if (! $filestoreFile->access_log) {
            return null;
        }

        $data = $logData;
        $data['filestore_file_id'] = $filestoreFile->id;
        $data['as_download'] = true;

        return $this->model->create($data);
    }

    public function createAsViewed(FilestoreFile $filestoreFile, array $logData): ?FilestoreFileAccess
    {
        if (! $filestoreFile->access_log) {
            return null;
        }

        $data = $logData;
        $data['filestore_file_id'] = $filestoreFile->id;
        $data['as_view'] = true;

        return $this->model->create($data);
    }

    private function create(array $data): FilestoreFileAccess
    {
        return $this->model->create($data);
    }
}
