<?php

namespace Yormy\FilestoreLaravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToListContents;

class VerifySetup extends Command
{
    protected $signature = 'filestore:verify';

    protected $description = 'Checking setup and if it is secure';

    public function handle()
    {
        $disk = 'digitalocean';

        if ($this->canStore($disk)) {
            $this->info('Storing allowed - OKE');
        } else {
            $this->error('Storing not allowed, add: s3:PutObject to s3 policy (or credentials are wrong)');
        }

        if ($this->canGet($disk)) {
            $this->info('Getting allowed - OKE');
        } else {
            $this->error('Getting not allowed, add: s3:GetObject to s3 policy (or credentials are wrong)');
        }

        if ($this->canDelete($disk)) {
            $this->info('Deleting allowed - OKE');
        } else {
            $this->error('Deleting not allowed, add: s3:DeleteObject to s3 policy (or credentials are wrong)');
        }

        if ($this->canListContents($disk)) {
            $this->error('Content listing not needed, remove: s3:ListBucket from s3 policy (or root user was used, create a new user)');
        } else {
            $this->info('Content listing disabled - OKE');
        }

        if ($this->failsIncorrectCredentials($disk)) {
            $this->info('Correct credentials required - OKE');
        } else {
            $this->error('Seems to be an open policy (or root user used, create a new user). Enhance restrictions by adding a user to S3 and login with that user only');
        }
    }

    private function failsIncorrectCredentials($disk): bool
    {
        $config = config('filesystems.disks');
        $currentDisk = $config[$disk];
        $currentDisk['key'] = '';

        config(['filesystems.disks.test-s3' => $currentDisk]);

        return Storage::disk('test-s3')->put('test-upload.txt', 'This is a test file.');
    }

    private function canListContents(string $disk): bool
    {
        $disk = Storage::disk($disk);
        try {
            $disk->allFiles();
        } catch (UnableToListContents $e) {
            return false;
        }

        return true;
    }

    private function canStore(string $disk): bool
    {
        return Storage::disk($disk)->put('test-upload.txt', 'This is a test file.');
    }

    private function canGet(string $disk): bool
    {
        Storage::disk($disk)->put('test-upload.txt', 'This is a test file.');

        return (bool) Storage::disk($disk)->get('test-upload.txt');
    }

    private function canDelete(string $disk): bool
    {
        Storage::disk($disk)->put('test-upload.txt', 'This is a test file.');

        return (bool) Storage::disk($disk)->delete('test-upload.txt');
    }
}
