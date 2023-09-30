<?php

namespace Yormy\FilestoreLaravel\Domain\Upload\Observers\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileDownloadWrongVariantEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        private string $variant,
    ) {
        // ...
    }

    public function getData()
    {
        return $this->variant;
    }
}
