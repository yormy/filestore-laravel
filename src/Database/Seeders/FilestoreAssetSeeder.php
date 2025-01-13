<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;

class FilestoreAssetSeeder extends Seeder
{
    public function run(): void
    {
        $filename = __DIR__.'/Data/sylvester.png';
        $file = new UploadedFile(
            $filename,
            basename($filename),
        );

        UploadFileService::make($file)
            ->sanitize()
            ->name('plain')
            ->saveToPersistent('seeded');

        UploadFileService::make($file)
            ->sanitize()
            ->name('encrypted')
            ->saveEncryptedToPersistent('seeded');
    }
}
