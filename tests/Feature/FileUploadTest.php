<?php

namespace Yormy\FilestoreLaravel\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileUploadTest extends TestCase
{
    use FileTrait;
    use UserTrait;

    /**
     * @test
     *
     * @group file-download
     */
    public function Upload_FileTooLarge_Exception(): void
    {
        $user = $this->createUser();
        Storage::fake('avatars');

        config(['filestore.max_file_size_kb' => 400]);

        $this->expectException(ValidationException::class);
        $response = $this->json('POST', route('api.upload', []), [
            'file' => UploadedFile::fake()->image('avatar.jpg')->size(3000),
        ]);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function Upload_UnsupportedMime_Exception(): void
    {
        $user = $this->createUser();
        Storage::fake('avatars');

        $this->expectException(ValidationException::class);
        $response = $this->json('POST', route('api.upload', []), [
            'file' => UploadedFile::fake()->image('avatar.jog')->size(300),
        ]);
    }
}
