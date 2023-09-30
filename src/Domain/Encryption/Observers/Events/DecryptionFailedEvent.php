<?php

namespace Yormy\FilestoreLaravel\Domain\Encryption\Observers\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DecryptionFailedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        private string $filename,
        private ?string $disk = null,
    ) {
        // ...
    }
}
