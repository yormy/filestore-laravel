<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Upload\Jobs;

use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Shared\Models\MemberFile;
use Yormy\FilestoreLaravel\Domain\Upload\Observers\Events\FileMovedToPersistentEvent;

class MoveFileToPersistentDiskJob
{
    public function __construct(
        private MemberFile $uploadedFileData,
        private string $sourcefile,
        private ?string $sourceDisk = null,
        private ?string $destination = null,
        private ?string $destinationDisk = null,
    ) {
        if (! $this->sourceDisk) {
            $this->sourceDisk = config('filestore.storage.local.disk');
        }

        if (! $this->destinationDisk) {
            $this->destinationDisk = config('filestore.storage.persistent.disk');
        }
    }

    public function handle()
    {
        $this->move($this->sourcefile);
    }

    public function move(string $sourcefile)
    {
        $destination = $sourcefile;
        if ($this->destination) {
            $destination = $this->destination;
        }

        Storage::disk($this->destinationDisk)->writeStream($destination, Storage::disk($this->sourceDisk)->readStream($sourcefile));

        Storage::disk($this->sourceDisk)->delete($sourcefile);

        $this->uploadedFileData->disk = $this->destinationDisk;
        $this->uploadedFileData->save();

        event(new FileMovedToPersistentEvent($sourcefile));
    }
}
