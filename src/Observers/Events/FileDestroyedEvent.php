<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Observers\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileDestroyedEvent {
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        readonly string $filename,
        readonly ?string $disk = null,
    ) {
        // ...
    }
}
