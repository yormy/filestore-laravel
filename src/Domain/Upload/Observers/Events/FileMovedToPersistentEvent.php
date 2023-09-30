<?php

namespace Yormy\FilestoreLaravel\Domain\Upload\Observers\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileMovedToPersistentEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        private string $filename,
        private ?string $disk = null,
    ) {
        // ...
    }
}
