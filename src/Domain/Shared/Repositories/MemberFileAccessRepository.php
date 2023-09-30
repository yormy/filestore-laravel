<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Shared\Repositories;

use Yormy\FilestoreLaravel\Domain\Shared\Models\MemberFile;
use Yormy\FilestoreLaravel\Domain\Shared\Models\MemberFileAccess;

class MemberFileAccessRepository
{
    public function __construct(private ?MemberFileAccess $model = null)
    {
        if (! $model) {
            $this->model = new MemberFileAccess();
        }
    }

    public function createAsDownloaded(MemberFile $memberFile, array $logData): ?MemberFileAccess
    {
        if (! $memberFile->access_log) {
            return null;
        }

        $data = $logData;
        $data['member_file_id'] = $memberFile->id;
        $data['as_download'] = true;

        return $this->model->create($data);
    }

    public function createAsViewed(MemberFile $memberFile, array $logData): ?MemberFileAccess
    {
        if (! $memberFile->access_log) {
            return null;
        }

        $data = $logData;
        $data['member_file_id'] = $memberFile->id;
        $data['as_view'] = true;

        return $this->model->create($data);
    }

    private function create(array $data): MemberFileAccess
    {
        return $this->model->create($data);
    }
}
