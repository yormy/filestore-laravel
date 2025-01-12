<?php
namespace Yormy\FilestoreLaravel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;

class CleanupTempJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $disk,
        public readonly string $path)
    {
        // ...
    }

    public function handle()
    {
        Storage::disk($this->disk)->delete($this->path);
    }
}
